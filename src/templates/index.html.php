<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Graphy</title>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
</head>
<body>

<form id="generate-form" action="generate" method="post">

    <div style="float: left; margin: 10px; border: 2px solid black; padding: 5px">
        <table style="border:1px" border="1">
            <tbody>
            <?php foreach ($workers as $id => $name): ?>
                <tr>
                    <td>
                        <label>
                            <input name="<?= $id ?>" type="checkbox">
                            <?= $name ?>
                        </label>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="float: left; margin: 10px; border: 2px solid black; padding: 5px">
        <script>
            $(document).ready(function () {
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                document.getElementById('generation-date').valueAsDate = tomorrow;

                if ((tomorrow.getDay() === 0) || (tomorrow.getDay() === 6)) {
                    document.getElementById('generation-team3count').valueAsNumber = 5;
                    document.getElementById('generation-team2count').valueAsNumber = 3;
                } else {
                    document.getElementById('generation-team3count').valueAsNumber = 8;
                    document.getElementById('generation-team2count').valueAsNumber = 0;
                }
            })
        </script>
        <div align="right">
            <div>
                <label>
                    Дата
                    <input id="generation-date" type="date" name="date" style="width: 120px">
                </label>
            </div>
            <div>
                <label>
                    По три людини
                    <input id="generation-team3count" type="number" name="team3count"
                           style="width: 40px">
                </label>
            </div>
            <div><label>
                    По дві людини
                    <input id="generation-team2count" type="number" name="team2count"
                           style="width: 40px">
                </label>
            </div>
            <div>
                <input type="submit" value="Згенерувати" style="width: 100%">
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            const form = $('#generate-form');
            const resultContainer = $('#generation-result-container');

            form.submit(
                event => {
                    event.preventDefault();
                    $.post('/generate', form.serialize(), function (html) {
                        resultContainer.empty();
                        resultContainer.append(html);
                    });
                });
        });
    </script>

</form>

<div style="float: left">

    <div id="generation-result-container">
    </div>

</div>

</body>
</html>
