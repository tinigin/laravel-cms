<!-- jQuery -->
<script src="{{ asset('assets/cms/plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('assets/cms/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets/cms/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('assets/cms/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/cms/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('assets/cms/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('assets/cms/plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('assets/cms/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- Bootstrap-select -->
<script src="{{ asset('assets/cms/plugins/twbs-select/1.13.12/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('assets/cms/plugins/twbs-select/1.13.12/i18n/defaults-ru_RU.min.js') }}"></script>
<!-- Sortable -->
<script src="{{ asset('assets/cms/plugins/sortablejs/1.15.0/sortable.min.js') }}"></script>
<!-- JS Cookie -->
<script src="{{ asset('assets/cms/plugins/js.cookie/js.cookie.min.js') }}"></script>
<!-- Sweet alert 2 -->
<script src="{{ asset('assets/cms/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- BS custom file input -->
<script src="{{ asset('assets/cms/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<!-- Lightbox -->
<script src="{{ asset('assets/cms/plugins/lightbox/2.11.1/js/lightbox.min.js') }}"></script>
<!-- Bootbox -->
<script src="{{ asset('assets/cms/plugins/bootbox/bootbox.all.min.js') }}"></script>
<!-- TinyMCE -->
<script src="{{ asset('assets/cms/plugins/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea.wysiwyg', // Replace this CSS selector to match the placeholder element for TinyMCE
        language: 'ru',
        language_url: '{{ asset('assets/cms/plugins/tinymce/langs/ru.js') }}',
        height: 500,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table directionality emoticons template paste"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image imagetools | media fullpage emoticons",
        browser_spellcheck: true,
        image_list: $('textarea.wysiwyg').parents('form').attr('data-images-url'),
        convert_urls: false
    });
</script>

<!-- AdminLTE App -->
<script src="{{ asset('assets/cms/js/adminlte.js') }}"></script>
<!-- App -->
<script src="{{ asset('assets/cms/js/app.js') }}"></script>

<!-- AdminLTE for demo purposes -->
{{--<script src="{{ asset('assets/cms/js/demo.js') }}"></script>--}}
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
{{--<script src="{{ asset('assets/cms/js/pages/dashboard.js') }}"></script>--}}
