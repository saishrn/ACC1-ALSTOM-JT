<?php

$sshCmd = '/usr/bin/ssh '
        . '-i /var/sshkeys_nobody/id_rsa_jtmon '
        . '-o StrictHostKeyChecking=no '
        . '-o UserKnownHostsFile=/dev/null '
        . '-o LogLevel=ERROR '
        . 'oracle1@ibmplmdusd80 '
        . '"cd /home/oracle1; bash ./JTVProcessed.sh" 2>&1';

$output = shell_exec($sshCmd);

$lines = explode("\n", trim($output));
$rows  = array();

foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }

    $parts = explode('|', $line);

    if (count($parts) == 5) {
        $rows[] = $parts;
    }
}

$refreshed = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html>
<head>
    <title>JT Processed Status</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f2f2f2;
        }
        .container {
            width: 90%;
            margin: 20px auto;
        }
        h2 {
            margin-bottom: 5px;
        }
        .timestamp {
            font-size: 13px;
            color: #555;
            margin-bottom: 15px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background-color: #ffffff;
        }
        th {
            background-color: #333;
            color: #ffffff;
            padding: 10px;
            border: 1px solid #666;
        }
        td {
            padding: 8px;
            border: 1px solid #ccc;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #eeeeee;
        }
        .db {
            color: #a00000;
            font-weight: bold;
        }
    </style>
</head>

<body>
<div class="container">

    <h2>JT Processed Status (Yesterday)</h2>
    <div class="timestamp">
        Last refreshed (server time): <?php echo $refreshed; ?>
    </div>

    <table>
        <tr>
            <th>Database</th>
            <th>Normal</th>
            <th>Extend</th>
            <th>Nolimit</th>
                        <th>Total</th>
        </tr>

        <?php foreach ($rows as $row): ?>
        <tr>
            <td class="db"><?php echo htmlspecialchars($row[0]); ?></td>
            <td><?php echo (int)$row[1]; ?></td>
            <td><?php echo (int)$row[2]; ?></td>
            <td><?php echo (int)$row[3]; ?></td>
                        <td><strong><?php echo (int)$row[4]; ?></strong></td>
        </tr>
        <?php endforeach; ?>

    </table>

</div>
</body>
</html>
