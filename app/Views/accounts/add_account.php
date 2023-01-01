<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $page_title ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="employee_id">Employee Name</label>
                            <input type="text" name="employee_id" id="employee_id" class="form-control" placeholder="" aria-describedby="helpId">
                            <small id="small_employee_id" class="text-muted"></small>
                        </div>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Enter here..." aria-describedby="small_">
                            <small id="small_username" class="text-muted"></small>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="text" name="password" id="password" class="form-control" placeholder="Enter here..." aria-describedby="small_">
                            <small id="small_password" class="text-muted"></small>
                        </div>

                        <div class="form-group">
                            <label for="access_level">Access Level</label>
                            <input type="text" name="access_level" id="access_level" class="form-control" placeholder="" aria-describedby="small_">
                            <small id="small_access_level" class="text-muted"></small>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</div>