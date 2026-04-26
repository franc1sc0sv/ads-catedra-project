#!/usr/bin/env bash
set -euo pipefail

# Read hook input from stdin
input=$(cat)

# Extract file_path from JSON input
file_path=$(echo "$input" | grep -o '"file_path":"[^"]*"' | head -1 | cut -d'"' -f4)

# Also try tool_input.file_path format
if [ -z "$file_path" ]; then
  file_path=$(echo "$input" | grep -o '"path":"[^"]*"' | head -1 | cut -d'"' -f4)
fi

[ -z "$file_path" ] && exit 0

# Only act on agent-os/specs/**/*.md
if ! echo "$file_path" | grep -qE 'agent-os/specs/[^/]+/[^/]+/.*\.md$'; then
  exit 0
fi

# Extract domain and feature from path
# Expected: .../agent-os/specs/{domain}/{feature}/...
specs_relative=$(echo "$file_path" | sed 's|.*agent-os/specs/||')
domain=$(echo "$specs_relative" | cut -d'/' -f1)
feature=$(echo "$specs_relative" | cut -d'/' -f2)
filename=$(basename "$file_path")

# Resolve project root (two levels up from .claude/hooks/)
script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
project_root="$(cd "$script_dir/../.." && pwd)"
index_file="$project_root/agent-os/specs/$domain/_index.md"

warnings=()

# Check _index.md exists
if [ ! -f "$index_file" ]; then
  warnings+=("⚠ spec-integrity: _index.md missing for domain '$domain' — create agent-os/specs/$domain/_index.md")
else
  # Check feature is listed in _index.md
  if ! grep -q "$feature" "$index_file" 2>/dev/null; then
    warnings+=("⚠ spec-integrity: feature '$feature' not listed in $domain/_index.md")
  fi
fi

# If tasks.md: check sub-spec links resolve
if [ "$filename" = "tasks.md" ] && [ -f "$file_path" ]; then
  feature_dir="$project_root/agent-os/specs/$domain/$feature"
  # Extract markdown links pointing to sub-specs/
  while IFS= read -r link; do
    target="$feature_dir/$link"
    if [ ! -f "$target" ]; then
      warnings+=("⚠ spec-integrity: tasks.md references missing file: sub-specs/$link")
    fi
  done < <(grep -oE '\(sub-specs/[^)]+\)' "$file_path" 2>/dev/null | tr -d '()' | sed 's|sub-specs/||')
fi

# Print local warnings
for w in "${warnings[@]}"; do
  echo "$w" >&2
done

# Call Haiku via claude CLI for deeper cross-reference check
if command -v claude &>/dev/null && [ -f "$index_file" ] && [ -f "$file_path" ]; then
  index_content=$(cat "$index_file")
  spec_content=$(cat "$file_path")

  result=$(printf "## Domain index (_index.md):\n%s\n\n---\n\n## Modified file (%s):\n%s" \
    "$index_content" "$filename" "$spec_content" \
    | claude -p "You are a spec integrity checker. Given the domain index and the modified spec file, verify:
1. Is this feature listed in the _index.md?
2. Do all sub-spec references in a tasks.md point to plausible file paths?
3. Are there any obvious cross-reference inconsistencies?

Reply with a single line: OK — if everything looks consistent.
Reply with a bullet list of violations if issues are found. Be concise." \
    --model claude-haiku-4-5-20251001 2>/dev/null || true)

  if [ -n "$result" ] && [ "$result" != "OK" ] && ! echo "$result" | grep -qi "^ok"; then
    echo "⚠ spec-integrity (Haiku):" >&2
    echo "$result" >&2
  fi
fi

exit 0
