<div class="row">
    <div class="col-12">                    
        <div class="card">
            <div class="card-header d-flex flex-column">
                <h4 class="card-title text-bold mr-3">List of Modules with Mail Notification</h4>
                <div class="d-flex justify-content-end align-items-center">
                    <div>
                        <label class="required mr-2" for="hostname">Is Mail Sending Enabled?</label>
                        <input type="checkbox" name="is_enable" value="<?= $mail['is_enable']; ?>" <?= $mail['is_enable'] === 'YES' ? 'checked' : ''; ?> data-mail_config_id="<?= $mail['mail_config_id']; ?>" data-bootstrap-switch>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p>
                    <strong>Note:</strong> 
                    In <strong>CC Recipients</strong>, if multiple emails - separate it by comma (e.g. mail@email.com, mail2@email.com). <br>
                </p>
                <div class="table-responsive">
                    <table id="mail_list_table" class="table table-hover table-striped nowrap align-items-middle" width="100%">
                        <thead class="nowrap">
                            <tr>
                                <th>Module Name</th>
                                <?php if (is_developer()): ?>
                                    <th>Has Mail Notif?</th>
                                    <th>Is Mail Notif Enabled?</th>
                                <?php endif; ?>
                                <th width="50%">CC Recipients</th>
                                <th class="text-center">Save</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (is_developer()): ?>
                                <?php foreach ($modules as $key => $val): ?>
                                    <?php
                                        $_mail_notifs   = check_param($mail_notifs, $key, '', true);
                                        $has_mail_notif = check_param($_mail_notifs, 'has_mail_notif');
                                        $has_mail_notif = $has_mail_notif ? 1 : 0;
                                        $is_enabled     = check_param($_mail_notifs, 'is_mail_notif_enabled');
                                        $is_enabled     = $is_enabled ? 1 : 0;
                                        $cc_recipients  = check_param($_mail_notifs, 'cc_recipients', '', true);
                                        $data           = 'data-module_code="'.$key.'"';
                                        $data2          = $data . ' data-has_mail_notif="'.$has_mail_notif.'"';
                                        $params         = "'{$key}', '{$has_mail_notif}', '{$is_enabled}'";
                                    ?>
                                    <tr id="ROW_<?= $key ?>">
                                        <td><?= $val ?></td>
                                        <td>
                                            <input type="checkbox" name="has_mail_notif" value="<?= $has_mail_notif ?>" data-bootstrap-switch <?= $has_mail_notif ? 'checked' : '' ?> <?= $data ?>>
                                        </td>
                                        <td>
                                            <input type="checkbox" name="is_mail_notif_enabled" value="<?= $is_enabled ?>" <?= $is_enabled ? 'checked' : '' ?> data-bootstrap-switch <?= $data2 ?>>
                                        </td>
                                        <td>
                                            <textarea class="form-control" name="recipients" rows="2" placeholder="Enter CC Recipients"><?= $cc_recipients ?? ''; ?></textarea>
                                            <small class="text-danger text-bold alert-recipients"></small>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm" onclick="save(<?= $params ?>)"><i class="fas fa-check"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?> 
                                <?php foreach ($mail_notifs as $key => $val): ?>
                                    <?php
                                        $params = "'{$key}', '{$val['has_mail_notif']}', '{$val['is_mail_notif_enabled']}'";
                                    ?>
                                    <tr id="ROW_<?= $key ?>">
                                        <td><?= get_modules($key) ?></td>
                                        <td>
                                            <textarea class="form-control" name="recipients" rows="2" placeholder="Enter CC Recipients"><?= $val['cc_recipients'] ?? ''; ?></textarea>
                                            <small class="text-danger text-bold alert-recipients"></small>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm" onclick="save(<?= $params ?>)"><i class="fas fa-check"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>