{% if isMobile == 0 %}
    <script>
        $(function () {
            $('#adminForm .dataTable tbody td.view-bug-tracking a').each(function () {
                $(this).attr('data-toggle', 'modal');
                $(this).attr('data-target', '#myModal');
            });

            $(document.body).on('hidden.bs.modal', function () {
                $('#myModal').removeData('bs.modal')
            });
        });

        function goToDetailBug(element){
            window.location.href = $(element).attr('href');
        }

        $('body').on('click', '#reloadTrackingPage', function(){
            window.location.reload(true);
        });

        $('body').on('click', '#z-change-status-tracking', function(){
            blockTracking();
            var request = $.ajax({
                url: '/admin/bugtracking/index/changestatustracking/' +  $(this).attr('data-content') + '/' ,
                type: "POST",
                data: {
                    bug_tracking_status_id : $('#bug_tracking_status_id').val()
                },
                dataType: 'html'
            });

            request.done(function (data) {
                var message = '';
                if(data == '1'){
                    message = 'Change status success!';
                }else{
                    message = 'Change status error!';
                }
                unblockTracking(message);
            });

            request.fail(function(){
                    var message = 'Cannot connect internet, please try again later!';
                unblockTracking(message);
            });
        });

        function unblockTracking(message){
            setTimeout(function(){
                $('#myModal').unblock();
                $('#myModal').modal('hide');
                $('#messageModal .modal-body').html(message);
                $('#messageModal').modal('show');
            },1000);
        }



        function blockTracking(tableTracking){
            $('#myModal').block({
                overlayCSS: {
                    backgroundColor: '#fff'
                },
                message: '<img src="/media/default/ajax-widget-loader.gif" />',
                css: {
                    border: 'none',
                    color: '#333',
                    background: 'none'
                }
            });
        }
    </script>
{% endif %}