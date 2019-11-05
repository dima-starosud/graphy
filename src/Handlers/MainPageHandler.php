<?php


namespace Staro\Graphy\Handlers;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Staro\Graphy\Logic\Worker;
use Staro\Graphy\Logic\WorkersProvider;
use Zend\Diactoros\Response\HtmlResponse;

final class MainPageHandler implements RequestHandlerInterface {
    /**
     * @var WorkersProvider
     */
    private $workersProvider;

    public function __construct(WorkersProvider $workersProvider) {
        $this->workersProvider = $workersProvider;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        ob_start();
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Graphy</title>

            <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        </head>
        <body>

        <div style="float: left; margin: 10px; border: 2px solid black; padding: 5px">
            <?php $this->renderUploadForm() ?>
        </div>

        <form id="generate-form">

            <div style="float: left; margin: 10px; border: 2px solid black; padding: 5px">
                <?php $this->renderWorkersTable( Worker::CARRIER ) ?>
            </div>

            <div style="float: left; margin: 10px; border: 2px solid black; padding: 5px">
                <?php $this->renderWorkersTable( Worker::DRIVER ) ?>
            </div>

            <div style="float: left; margin: 10px; border: 2px solid black; padding: 5px">
                <?php $this->renderGenerationInput() ?>
            </div>

        </form>

        <div style="float: left; margin: 10px; border: 2px solid black; padding: 5px">
            <?php $this->renderGenerationResult() ?>
        </div>

        </body>
        </html>

        <?php
        return new HtmlResponse( ob_get_clean() );
    }

    private function renderUploadForm() {
        ?>
        <form action="upload" method="post">
            <div>
                <textarea name="text" style="width: 100%"></textarea>
            </div>
            <div>
                <input type="submit" value="Завантажити таблицю" style="width: 100%; alignment: right">
            </div>
        </form>
        <?php
    }

    private function renderWorkersTable($post) {
        $workers = array_filter( $this->workersProvider->getWorkers(), function ($worker) use ($post) {
            return $worker->getPost() === $post;
        } );
        ?>
        <table style="border:1px" border="1">
            <tbody>
            <?php foreach ($workers as $worker): ?>
                <tr>
                    <td>
                        <label>
                            <input name="<?= $worker->getIndex() ?>" type="checkbox">
                            <?= $worker->getName() ?>
                        </label>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }

    private function renderGenerationInput() {
        $workers = [];
        foreach ($this->workersProvider->getWorkers() as $worker) {
            $workers[$worker->getIndex()] = $worker->getName();
        }
        ?>
        <script>
            // noinspection JSAnnotator
            const workers = <?= json_encode( $workers ) ?>;

            $(document).ready(function () {
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                document.getElementById('generation-date').valueAsDate = tomorrow;

                if ((tomorrow.getDay() === 0) || (tomorrow.getDay() === 6)) {
                    document.getElementById('generation-team3count').checked = true;
                    document.getElementById('generation-team2count').checked = true;
                } else {
                    document.getElementById('generation-team3count').checked = true;
                    document.getElementById('generation-team2count').checked = false;
                }

                const form = $('#generate-form');
                const table = $('#generation-table-body');

                form.submit(
                    event => {
                        event.preventDefault();
                        $.post('/generate', form.serialize(), function (data) {
                            table.empty();
                            for (const ids of data) {
                                let team = ids.map(id => workers[id]).join(' - ');
                                table.append(`
                                    <tr><td>
                                        <label>
                                            <input type="checkbox"/>
                                            ${team}
                                        </label>
                                    </td></tr>`);
                            }
                        });
                    }
                );
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
                    <input id="generation-team3count" type="checkbox" name="team3count"
                           style="width: 40px">
                </label>
            </div>
            <div>
                <label>
                    По дві людини
                    <input id="generation-team2count" type="checkbox" name="team2count"
                           style="width: 40px">
                </label>
            </div>
            <div>
                <input type="submit" value="Згенерувати" style="width: 100%">
            </div>
        </div>
        <?php
    }

    private function renderGenerationResult() {
        ?>
        <div style="width: 100%">
            <table style="border:1px; width: 100%" border="1">
                <tbody id="generation-table-body">
                </tbody>
            </table>
        </div>
        <?php
    }
}
