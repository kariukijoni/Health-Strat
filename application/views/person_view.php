<!DOCTYPE html>
<html>
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>KIJIJI PHARMACY</title>
        <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') ?>" rel="stylesheet">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head> 
    <body>
        <div class="container" style="padding-top: 10px">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <center> 
                        <b>
                            <h3>KIJIJI PHARMACY MANAGEMENT</h3>
                        </b>
                    </center>
                </div>
            </div>
            <h3>Patients Info</h3>
            <br />
            <button class="btn btn-primary" onclick="add_person()"><i class="glyphicon glyphicon-plus"></i> Add Patient</button>
            <button class="btn btn-success" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Refresh</button>
            <br />
            <br />
            <table id="table" class="table table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date Of Birth</th>
                        <th>Gender</th>
                        <th>Type Of Service</th>
                        <th>General Observations</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>

                
                <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>Date Of Birth</th>
                        <th>Gender</th>
                        <th>Type Of Service</th>
                        <th>General Observations</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <script src="<?php echo base_url('assets/jquery/jquery-2.1.4.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.js') ?>"></script>
        <script src="<?php echo base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ?>"></script>


        <script type="text/javascript">

                var save_method; //for save method string
                var table;

                $(document).ready(function () {

                    //datatables
                    table = $('#table').DataTable({

                        "processing": true,
                        "serverSide": true,
                        "order": [],

                        // Load data for the table's content from an Ajax source
                        "ajax": {
                            "url": "<?php echo site_url('person/ajax_list') ?>",
                            "type": "POST"
                        },

                        //Set column definition initialisation properties.
                        "columnDefs": [
                            {
                                "targets": [-1], //last column
                                "orderable": false,
                            },
                        ],

                    });

                    //datepicker
                    $('.datepicker').datepicker({
                        autoclose: true,
                        format: "yyyy-mm-dd",
                        todayHighlight: true,
                        orientation: "top auto",
                        todayBtn: true,
                        todayHighlight: true,
                    });

                    //set input/textarea/select event when change value, remove class error and remove text help block 
                    $("input").change(function () {
                        $(this).parent().parent().removeClass('has-error');
                        $(this).next().empty();
                    });
                    $("textarea").change(function () {
                        $(this).parent().parent().removeClass('has-error');
                        $(this).next().empty();
                    });
                    $("select").change(function () {
                        $(this).parent().parent().removeClass('has-error');
                        $(this).next().empty();
                    });

                });



                function add_person()
                {
                    save_method = 'add';
                    $('#form')[0].reset(); // reset form on modals
                    $('.form-group').removeClass('has-error');
                    $('.help-block').empty();
                    $('#modal_form').modal('show');
                    $('.modal-title').text('Add Patient');
                }

                function edit_person(id)
                {
                    save_method = 'update';
                    $('#form')[0].reset(); // reset form on modals
                    $('.form-group').removeClass('has-error'); // clear error class
                    $('.help-block').empty(); // clear error string

                    //Ajax Load data from ajax
                    $.ajax({
                        url: "<?php echo site_url('person/ajax_edit/') ?>/" + id,
                        type: "GET",
                        dataType: "JSON",
                        success: function (data)
                        {

                            $('[name="id"]').val(data.id);
                            $('[name="name"]').val(data.name);
                            $('[name="dob"]').datepicker('update', data.dob);
                            $('[name="gender"]').val(data.gender);
                            $('[name="type_of_service"]').val(data.type_of_service);
                            $('[name="general_observations"]').val(data.general_observation);

                            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                            $('.modal-title').text('Edit Patient'); // Set title to Bootstrap modal title

                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            alert('Error get data from ajax');
                        }
                    });
                }

                function reload_table()
                {
                    table.ajax.reload(null, false); //reload datatable ajax 
                }

                function save()
                {
                    $('#btnSave').text('saving...');
                    $('#btnSave').attr('disabled', true);
                    var url;

                    if (save_method == 'add') {
                        url = "<?php echo site_url('person/ajax_add') ?>";
                    } else {
                        url = "<?php echo site_url('person/ajax_update') ?>";
                    }

                    // ajax adding data to database
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: $('#form').serialize(),
                        dataType: "JSON",
                        success: function (data)
                        {

                            if (data.status) //if success close modal and reload ajax table
                            {
                                $('#modal_form').modal('hide');
                                reload_table();
                            } else
                            {
                                for (var i = 0; i < data.inputerror.length; i++)
                                {
                                    $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                                    $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                                }
                            }
                            $('#btnSave').text('save');
                            $('#btnSave').attr('disabled', false);


                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            alert('Error adding / update data');
                            $('#btnSave').text('save');
                            $('#btnSave').attr('disabled', false); //set button enable 

                        }
                    });
                }

                function delete_person(id)
                {
                    if (confirm('Are you sure delete this data?'))
                    {
                        // ajax delete data to database
                        $.ajax({
                            url: "<?php echo site_url('person/ajax_delete') ?>/" + id,
                            type: "POST",
                            dataType: "JSON",
                            success: function (data)
                            {
                                //if success reload ajax table
                                $('#modal_form').modal('hide');
                                reload_table();
                            },
                            error: function (jqXHR, textStatus, errorThrown)
                            {
                                alert('Error deleting data');
                            }
                        });

                    }
                }

        </script>

        <!-- Bootstrap modal -->
        <div class="modal fade" id="modal_form" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">Person Form</h3>
                    </div>
                    <div class="modal-body form">
                        <form action="#" id="form" class="form-horizontal">
                            <input type="hidden" value="" name="id"/> 
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Name</label>
                                    <div class="col-md-9">
                                        <input name="name" placeholder="Name" class="form-control" type="text">
                                        <span class="help-block"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3">Date of Birth</label>
                                    <div class="col-md-9">
                                        <input name="dob" placeholder="YYYY-MM-DD" class="form-control datepicker" type="text">
                                        <span class="help-block"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3">Gender</label>
                                    <div class="col-md-9">
                                        <select name="gender" class="form-control">
                                            <option value="">--Select Gender--</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                        <span class="help-block"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3">Type Of Service</label>
                                    <div class="col-md-9">
                                        <select name="type_of_service" class="form-control">
                                            <option value="">--Select Type Of Service--</option>
                                            <option value="art">ART</option>
                                            <option value="prep">PREP</option>
                                            <option value="pep">PEP</option>
                                            <option value="oi">OI</option>
                                        </select>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">General Observations</label>
                                    <div class="col-md-9">
                                        <textarea name="general_observations" placeholder="General Observation" class="form-control"></textarea>
                                        <span class="help-block"></span>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- End Bootstrap modal -->
    </body>
</html>