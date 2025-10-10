<!DOCTYPE html>
<html>
<head>
    <title>Test Form</title>
</head>
<body>
    <form method="POST">
        <table>
            <tbody id="employmentBody">
                <tr>
                    <td><input type="text" name="company[]"></td>
                    <td><input type="text" name="position[]"></td>
                    <td><input type="text" name="reason_for_leaving[]"></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <button type="button" onclick="addRow()">Add Row</button>
        <button type="submit">Submit</button>
    </form>

    <script>
    function addRow() {
        const tableBody = document.getElementById('employmentBody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" name="company[]"></td>
            <td><input type="text" name="position[]"></td>
            <td><input type="text" name="reason_for_leaving[]"></td>
            <td><button type="button" onclick="this.closest('tr').remove()">Remove</button></td>
        `;
        tableBody.appendChild(newRow);
    }
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
    }
    ?>
</body>
</html>
