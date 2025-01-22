<script>
    $(document).on("click",".logout-btn",function(event) {
        event.preventDefault();
        var actionRoute =  "{{ setRoute('admin.logout') }}";
        var target      = "auth()->user()->id";
        var logout = `{{ __("Logout") }}`;
        var sureText = '{{ __("ru sure to") }}';
        var message     = `${sureText} <strong>${logout}</strong>?`;
        openDeleteModal(actionRoute,target,message,logout,"POST");
    });


</script>
