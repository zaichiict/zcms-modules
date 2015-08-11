{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <form action="{{ _baseUri }}{{ router.getRewriteUri() }}" method="post" id="adminForm" enctype="multipart/form-data">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-external-link-square"></i>
                    Form
                    <div class="panel-tools">
                        <a class="btn btn-xs btn-link panel-collapse collapses" href="#">
                        </a>
                    </div>
                </div>
                <div class="panel-body buttons-widget">
                    <div class="col-md-8" style="padding-left: 0;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"> Type <span class="symbol required"></span></label>
                                {{ form.render('bug_tracking_type_id') }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"> Priority <span class="symbol required"></span></label>
                                {{ form.render('bug_tracking_priority_id') }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"> Notify <span class="symbol required"></span></label>
                                {{ form.render('role_id') }}
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="col-md-12">
                            <label class="control-label"> Description <span class="symbol required"></span></label>
                            {{ form.render('description') }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <label class="control-label"> Image</label>

                            <div class="clearfix    "></div>
                            <div style="float: left">
                                <div class="product_image_preview" onclick="openFileBrowser(this)"><img
                                            src="{% if imageBug is defined %}{{ imageBug }}{% else %}/images/tmp/select-image.png{% endif %}"
                                            style="width: 250px; border: 1px solid #c0c0c0; padding: 2px; border-radius: 3px"></div>
                                <input type="file" name="image" style="display: none" onchange="readURL(this)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
{% endblock %}

{% block js_footer %}
    <script>
        $(function(){
           $('#bug_tracking_type_id').change(function(){
                if($(this).val() != ''){

                    $('.btn.btn-primary.btn-sm').html('<span class="glyphicon glyphicon-floppy-saved"></span> Add ' + $(this).find('option:selected').html());
                }
           });
        });
        function openFileBrowser(div) {
            $(div).next('input[type="file"]').trigger('click');
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(input).prev().children('img').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        //Override submit function
        ZCMS.submitForm = function (f) {
            if ("undefined" === typeof f && (f = document.getElementById("adminForm"), !f)) f = document.adminForm;
            if ("function" == typeof f.onsubmit) f.onsubmit();
            "function" == typeof f.fireEvent && f.fireEvent("submit");

            var hasErrorRequiredField = false;
            var ErrorRequiredFieldID = '';
            $('[required]').each(function () {
                if ($(this).val() == '') {
                    $(this).parent().addClass('has-error');
                    if ($(this).parent().find('.help-block').length == 0) {
                        $(this).parent().append('<span class="help-block">Please specify this field</span>');
                    } else {
                        $(this).parent().find('.help-block').css('display', 'block');
                    }
                    hasErrorRequiredField = true;
                    if (ErrorRequiredFieldID == '') {
                        ErrorRequiredFieldID = $(this).attr('id');
                    }

                } else {
                    $(this).parent().addClass('has-success').removeClass('has-error');
                    $(this).parent().find('.help-block').css('display', 'none');
                }
            });

            if (hasErrorRequiredField == true) {
                if (ErrorRequiredFieldID != '') {
                    $('#' + ErrorRequiredFieldID).focus();
                }
                return false;
            } else {
                ErrorRequiredFieldID = '';
            }

            f.submit();
            return false;
        };
    </script>
{% endblock %}
