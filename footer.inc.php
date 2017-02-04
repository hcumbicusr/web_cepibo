<!-- footer content -->
        <footer>
          <div class="pull-right">
            &copy; 2016 - Admin
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="<?php echo PATH.'/'; ?>vendors/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/jquery/dist/jquery.numeric.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/jquery/dist/jquery.validate.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>js/jquery.validate.methods.js"></script>
    <script src="<?php echo PATH.'/'; ?>js/jquery.validate.translate.js"></script>
    <!-- Bootstrap -->
    <script src="<?php echo PATH.'/'; ?>vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="<?php echo PATH.'/'; ?>vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="<?php echo PATH.'/'; ?>vendors/nprogress/nprogress.js"></script>

    <script src="<?php echo PATH.'/'; ?>vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>

    <!-- Chart.js -->
    <!--
    <script src="<?php echo PATH.'/'; ?>vendors/Chart.js/dist/Chart.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/Chart.js/dist/canvas-toBlob.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/Chart.js/dist/FileSaver.min.js"></script>
    -->
    <!-- jQuery Sparklines -->
    <!-- <script src="<?php echo PATH.'/'; ?>vendors/jquery-sparkline/dist/jquery.sparkline.min.js"></script> -->


    <!-- Select2 -->
    <script src="<?php echo PATH.'/'; ?>vendors/select2/dist/js/select2.full.min.js"></script>

    <!-- PNotify -->
    <script src="<?php echo PATH.'/'; ?>vendors/pnotify/dist/pnotify.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/pnotify/dist/pnotify.buttons.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/pnotify/dist/pnotify.nonblock.js"></script>

    <!-- Flot -->
    <!--
    <script src="<?php echo PATH.'/'; ?>vendors/Flot/jquery.flot.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/Flot/jquery.flot.pie.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/Flot/jquery.flot.time.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/Flot/jquery.flot.stack.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/Flot/jquery.flot.resize.js"></script>
    
    <script src="js/flot/jquery.flot.orderBars.js"></script>
    <script src="js/flot/date.js"></script>
    <script src="js/flot/jquery.flot.spline.js"></script>
    <script src="js/flot/curvedLines.js"></script>
    -->

    <!-- Datatables -->
    <script src="<?php echo PATH.'/'; ?>vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/datatables.net-scroller/js/datatables.scroller.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/jszip/dist/jszip.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/pdfmake/build/vfs_fonts.js"></script>

    <!-- jQuery Smart Wizard -->
    <script src="<?php echo PATH.'/'; ?>vendors/jQuery-Smart-Wizard/js/jquery.smartWizard.js"></script>


    <!-- bootstrap-daterangepicker -->
    <!-- <script src="<?php echo PATH.'/'; ?>js/moment/moment.min.js"></script> -->
    <!-- <script src="<?php echo PATH.'/'; ?>js/moment/moment-with-locales.js"></script> -->
    <!-- <script src="<?php echo PATH.'/'; ?>js/moment/moment.js"></script> -->
    <!-- <script src="<?php echo PATH.'/'; ?>js/datepicker/daterangepicker.js"></script> -->
    <script src="<?php echo PATH.'/'; ?>js/datepicker/bower_components/moment/min/moment-with-locales.js"></script>    
    <script src="<?php echo PATH.'/'; ?>js/datepicker/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>    
    
    <!-- Custom Theme Scripts -->
    <script src="<?php echo PATH.'/'; ?>js/custom.js"></script>
    <script type="text/javascript">
        moment().utcOffset(-0500); // UTC => Lima
        moment().format("L"); // formato es => DD/MM/YYYY
        $(document).ready(function(){
            $.PATH_PUBLIC = "<?php echo PATH."/"; ?>";
            $.PATH = "<?php echo PATH."/backend/"; ?>";
            $.USERNAME = "<?php echo $_SESSION['username']; ?>";
            $.ID_USER = "<?php echo $_SESSION['id_user']; ?>";
        });
    </script>
    <!-- Highcharts -->
    <script src="<?php echo PATH.'/'; ?>vendors/Highcharts/highcharts.js"></script>
    <script src="<?php echo PATH.'/'; ?>vendors/Highcharts/exporting.js"></script>
    

    <script src="<?php echo PATH.'/'; ?>js/allpage.js"></script>

    <script src="<?php echo PATH.'/'; ?>js/<?php echo $view; ?>.js"></script>

    
  </body>
</html>