<!-- JAVASCRIPT -->
    <script src="<?= base_url(); ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url(); ?>assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?= base_url(); ?>assets/libs/node-waves/waves.min.js"></script>
    <script src="<?= base_url(); ?>assets/libs/feather-icons/feather.min.js"></script>
    <script src="<?= base_url(); ?>assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <!-- <script src="<?= base_url(); ?>assets/js/plugins.js"></script> -->

    <!-- apexcharts -->
    <!-- <script src="<?= base_url(); ?>assets/libs/apexcharts/apexcharts.min.js"></script> -->

    <!-- Vector map-->
    <script src="<?= base_url(); ?>assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="<?= base_url(); ?>assets/libs/jsvectormap/maps/world-merc.js"></script>

    <!--Swiper slider js-->
    <!-- <script src="<?= base_url(); ?>assets/libs/swiper/swiper-bundle.min.js"></script> -->

    <!-- App js -->
    <script src="<?= base_url(); ?>assets/js/app.js"></script>
    <script src="<?= base_url(); ?>assets/code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/toastr/toastr.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/chosen/chosen.jquery.min.js"></script>

    <script src="<?= base_url(); ?>assets/js/colonia-otra.js?v=20261020"></script>

    <script type="text/javascript">

        window.mpdigitalAjaxErrorMessage = function (xhr, fallback) {
            var body = xhr && xhr.responseJSON;
            if (!body && xhr && xhr.responseText) {
                try { body = (xhr.responseText); } catch (_) {}
            }
            return (body && (body.message || body.error)) || fallback;
        };

        function formatMoney(amount, currencySymbol = '$', decimalPlaces = 2) {
            if(isNaN(amount)) return amount;
            return `${currencySymbol}${Number(amount).toFixed(decimalPlaces).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}`;
        }
    </script>
