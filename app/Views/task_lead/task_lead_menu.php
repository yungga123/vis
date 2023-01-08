<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Task Leads Monitoring</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <section class="content">
    <div class="container-fluid">
      <div class="row">

        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-success">
            <div class="inner">
              <h4>Add Project</h4>
              
              
              <p>Make sure customer is added.</p>
            </div>
            <div class="icon">
              <i class="fas fa-tasks"></i>
            </div>
            <!-- <a href="<?= site_url('tasklead-addproject') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a> -->
            <a href="<?= site_url('tasklead-addproject') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
          </div>

        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h4>Project List</h4>
              
              
              <p>View your projects here.</p>
            </div>
            <div class="icon">
              <i class="fas fa-tasks"></i>
            </div>
            <a href="<?= site_url('project-list') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h4>Booked Projects</h4>
              
              <p>View booked projects.</p>
            </div>
            <div class="icon">
              <i class="fas fa-tasks"></i>
            </div>
            <a href="<?= site_url('project-list-booked') ;?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>


      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h4>Project List (Consolidated)</h4>
              
              
              <p>Only Managers have access.</p>
            </div>
            <div class="icon">
              <i class="fas fa-tasks"></i>
            </div>
            <a href="<?= site_url('manager-project-list') ;?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h4>Booked Projects (Consolidated)</h4>
              
              <p>View booked projects.</p>
            </div>
            <div class="icon">
              <i class="fas fa-tasks"></i>
            </div>
            <a href="<?= site_url('manager-project-list-booked') ;?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

        


    </div>
  </section>

</div>

