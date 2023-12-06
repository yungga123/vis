<div class="row">
    <div class="col-12">                    
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">List of Modules with Mail Notification</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="mail_list_table" class="table table-hover table-striped nowrap" width="100%">
                        <thead class="nowrap">
                            <tr>
                                <th>Module Name</th>
                                <th>Has Mail Notif?</th>
                                <th>Is Mail Notif Enabled?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modules as $key => $val): ?>
                                <?php
                                    $_mail_notifs   = check_param($mail_notifs, $key, '', true);
                                    $has_mail_notif = check_param($_mail_notifs, 'has_mail_notif');
                                    $has_mail_notif = $has_mail_notif ? 1 : 0;
                                    $is_enabled     = check_param($_mail_notifs, 'is_mail_notif_enabled');
                                    $is_enabled     = $is_enabled ? 1 : 0;
                                    $data           = 'data-module_code="'.$key.'"';
                                    $data2          = $data . ' data-has_mail_notif="'.$has_mail_notif.'"';
                                ?>
                                <tr>
                                    <td><?= $val ?></td>
                                    <td>
                                        <input type="checkbox" name="has_mail_notif" id="HAS_<?= $key ?>" data-bootstrap-switch <?= $has_mail_notif ? 'checked' : '' ?> <?= $data ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="is_mail_notif_enabled" id="IS_<?= $key ?>" <?= $is_enabled ? 'checked' : '' ?> data-bootstrap-switch <?= $data2 ?>>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>