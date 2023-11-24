<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Notification - <?= $title ?></title>
</head>
<body style="font-family: 'Source Sans Pro',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol'; font-size: 0.9rem; font-weight: 400; color: #212529; background-color: #f1f1f18c;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 2.2rem 0;">
        <tbody>
            <tr>
                <td>
                    <div style="text-align: center;">
                        <h2 style="margin-top: 5px;"><?= $title ?></h2>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0" style="background-color: #fff; background-clip: border-box;  border: 0 solid rgba(0,0,0,.125); border-radius: 0.25rem; box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); margin: 0 auto; padding: 0.8rem; min-width: 570px;
                    ">
                        <tbody>
                            <tr>
                                <td>
                                    <div style="text-align: center; font-size: 1.2rem; text-decoration: underline; margin-bottom: 1rem; margin-top: 0.5rem;"><?= $module ?> Details</div>
                                    <?php foreach($details as $key => $val): ?>
                                        <div style="display: flex; justify-content: between; border-bottom: 1px solid #ccc; padding: 0.5rem;">
                                            <div style="font-weight: bold; width: 35%;"><?= $key ?>:</div>
                                            <div><?= $val ?></div>
                                        </div>
                                    <?php endforeach ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="text-align: center; margin-top: 1.5rem; margin-bottom: 0.5rem;">
                                        <div><?= $company_name ?? 'Vinculum Technologies' ?></div>
                                        <small><i>[This is auto generated. Please don't reply to this email!]</i></small>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>