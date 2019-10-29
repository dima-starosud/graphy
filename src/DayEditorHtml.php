<?php


namespace Staro\Graphy;


use Staro\Graphy\Logic\StaticWorkersProvider;

final class DayEditorHtml {
    /**
     * @var StaticWorkersProvider
     */
    private $workersProvider;

    function __construct(StaticWorkersProvider $workersProvider) {
        $this->workersProvider = $workersProvider;
    }

    function echoHtml($date, $teams) {
        $workers = $this->workersProvider->getWorkers();
        ?>
        <div style="float: left; margin: 10px; border: 2px solid black; padding: 5px">
            <div style="width: 100%">
                <?= $date ?>
                <div>
                    <?php foreach ($teams as $team) : ?>
                        <div style="margin: 5px; border: 2px solid black">
                            <?php foreach ($team as $member_id) : ?>
                                <div>
                                    <select>
                                        <?php foreach ($workers as $worker_id => $worker): ?>

                                            <option value="<?= $worker_id ?>" <?= $worker_id === $member_id ? 'selected' : '' ?>>
                                                <?= $worker ?>
                                            </option>

                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
