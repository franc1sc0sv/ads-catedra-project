{{-- Inline synchronous bootstrap for Alpine custom data factories.
     Loaded BEFORE Alpine's CDN script so uiSelect/uiToast are guaranteed
     to exist on window when Alpine evaluates x-data expressions. --}}
<script>
@php
    echo file_get_contents(resource_path('js/ui/select.js'));
    echo "\n";
    echo file_get_contents(resource_path('js/ui/toast.js'));
@endphp
</script>
