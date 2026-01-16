<?php
$rows = array();
$error = '';
$availableDates = array();

$sshCmdDates = '/usr/bin/ssh '
    . '-i /var/sshkeys_nobody/id_rsa_jtmon '
    . '-o StrictHostKeyChecking=no '
    . '-o UserKnownHostsFile=/dev/null '
    . '-o LogLevel=ERROR '
    . 'oracle1@ibmplmdusd80 '
    . '"cd /home/oracle1; java -jar JTVPendingDates.jar" 2>&1';

$dateOutput = shell_exec($sshCmdDates);

if (is_string($dateOutput) && trim($dateOutput) !== '') {
    $availableDates = explode("\n", trim($dateOutput));
}

if (isset($_POST['submit'])) {
    $selectedDate = $_POST['date'];
    if (!preg_match('/^\d{2}-[A-Za-z]{3}-\d{4}$/', $selectedDate)) {
        $error = 'Invalid date selection';
    } else {
        $sshCmdData = '/usr/bin/ssh '
            . '-i /var/sshkeys_nobody/id_rsa_jtmon '
            . '-o StrictHostKeyChecking=no '
            . '-o UserKnownHostsFile=/dev/null '
            . '-o LogLevel=ERROR '
            . 'oracle1@ibmplmdusd80 '
            . '"cd /home/oracle1; '
            . 'java -jar JTVPendingArchive.jar '
            . escapeshellarg($selectedDate)
            . '" 2>&1';

        $output = shell_exec($sshCmdData);

        if (is_string($output) && trim($output) !== '') {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;

                $parts = explode('|', $line);
                if (count($parts) == 5) {
                    $rows[] = $parts;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>JT Pending Archive</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
        :root {
            --page-bg: #e6f0ff;      /* mild blue background */
            --text: #222;
            --muted: #555;
            --table-border: #c9d4ea; /* subtle bluish border */
            --row-alt: #f0f7ff;      /* light blue zebra row */
            --header-bg: #243447;    /* dark header for contrast */
            --header-text: #ffffff;
            --danger: #a00000;       /* deep red for DB values */
            --brand: #2f6fed;        /* button color you used */
        }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
            background-color: var(--page-bg);
            margin: 0;
        }
        .container {
            width: 95%;
            max-width: 1100px;
            margin: 24px auto;
        }
        /* Top hero image */
        .hero {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
            margin-bottom: 16px;
        }
        h2 {
            margin: 6px 0 12px 0;
            color: var(--text);
            font-weight: 700;
        }
        .form-box {
            background-color: #ffffff;
            border: 1px solid var(--table-border);
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-right: 10px;
        }
        select {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #aaa;
            font-size: 14px;
            min-width: 200px;
        }
        input[type="submit"] {
            margin-left: 10px;
            padding: 7px 16px;
            font-size: 14px;
            background-color: var(--brand);
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #2457c6;
        }
        .meta {
            color: var(--muted);
            margin-bottom: 12px;
            font-size: 13px;
        }
        /* Center the table and keep it "normal" width */
        .table-wrap {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
        }
        table.archive {
            border-collapse: collapse;
            width: 760px;
            max-width: 100%;
            background: transparent;
            border: 1px solid var(--table-border);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        table.archive th {
            background-color: var(--header-bg);
            color: var(--header-text);
            padding: 10px;
            font-size: 14px;
            border: 1px solid #1c2a36;
            text-align: center;
            white-space: nowrap;
        }
        table.archive td {
            padding: 10px;
            border: 1px solid var(--table-border);
            font-size: 14px;
            text-align: center;
            background-color: #ffffff;
        }
        /* Zebra rows */
        table.archive tbody tr:nth-child(even) td {
            background-color: var(--row-alt);
        }
        /* Database values in bold red */
        table.archive td:nth-child(2) {
            color: var(--danger);
            font-weight: bold;
        }
        /* Last three numeric columns bold + centered (Normal, Extend, Nolimit) */
        table.archive td:nth-child(3),
        table.archive td:nth-child(4),
        table.archive td:nth-child(5) {
            font-weight: bold;
            color: #000;
            text-align: center;
        }
        .error {
            color: #c62828;
            margin-top: 10px;
            font-weight: bold;
        }
        .no-data {
            margin-top: 20px;
            font-style: italic;
            color: var(--muted);
        }
        @media (max-width: 820px) {
            table.archive { width: 100%; }
            table.archive th, table.archive td { padding: 8px; font-size: 13px; }
        }
    </style>
</head>

<body>
<div class="container">
    <!-- Image first -->
    <img src="../images/AlstomImage.jpg" class=">
    <div class="form-box">
        <form method="post">
            <label>[JT Pending Archive] Select Date:</label>
            <select name="date" required>
                <?php foreach ($availableDates as $d): ?>
                    <option value="<?php echo htmlspecialchars($d); ?>"
                        <?php if (isset($selectedDate) && $selectedDate == $d) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($d); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" name="submit" value="Get Data">
        </form>
        <div class="meta">Last refreshed (server time): <?php echo date('Y-m-d H:i:s'); ?></div>
    </div>
    <?php if ($error != ''): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (!empty($rows)): ?>
        <div class="table-wrap">
            <table class="archive" aria-label="JT Pending Archive Table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Database</th>
                        <th>Normal</th>
                        <th>Extend</th>
                        <th>Nolimit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r[0]); ?></td>
                        <td><?php echo htmlspecialchars($r[1]); ?></td>
                        <td><?php echo htmlspecialchars($r[2]); ?></td>
                        <td><?php echo htmlspecialchars($r[3]); ?></td>
                        <td><?php echo htmlspecialchars($r[4]); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (isset($_POST['submit']) && $error == ''): ?>
        <p class="no-data">No data found for selected date.</p>
    <?php endif; ?>
</div>
</body>
</html>
