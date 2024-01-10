<div class="card card-outline card-info clock-attendance">
    <div class="card-header">
        <h4 class="card-title">Attandance</h4>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-1">
            <div><strong>Office Time:</strong> <?= $office_hours ?? '' ?></div>
        </div>
        <div class="d-flex justify-content-start align-items-center">
            <input type="hidden" id="_clock_in_id">
            <button class="btn btn-info" id="btn_clock_in" onclick="attendanceClockIn()">
                Clock In <span></span>
            </button>
            <button class="btn btn-danger ml-2" id="btn_clock_out" onclick="attendanceClockOut()">
                Clock Out <span></span>
            </button>
        </div>
    </div>
</div>