<?php
namespace GBoudreau\HDHomeRun\Scheduler;

require_once 'init.inc.php';

global $parser;

$recordings = $parser->getRecordings();
usort($recordings, [__NAMESPACE__ . '\Recording', 'sortByDateTime']);

?>

<?php require 'head.inc.php' ?>

<div class="row">
    <main class="p-3">
        <h2>
            Scheduled Recordings
            <button class="btn btn-default" onclick="window.location.href='new.php'">Create new</button>
        </h2>
        <table class="table table-striped table-responsive">
            <thead>
            <tr>
                <th>When</th>
                <th>Duration</th>
                <th>Channel</th>
                <th>What</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $log_file = Config::get('LOG_FILE');
            if (!empty($log_file)) {
                $parser = new LogParser($log_file);
                $past_recordings = $parser->getRecordings();
                usort($past_recordings, [__NAMESPACE__ . '\Recording', 'sortByDateTime']);
                $recordings = array_merge($past_recordings, $recordings);

                // Remove duplicate recordings (ongoing recordings will be in both log and schedules file)
                $recordings = array_filter(
                    $recordings,
                    function ($v, $k) {
                        global $recordings;
                        for ($i=0; $i<$k; $i++) {
                            if ($recordings[$i]->getHash() == $v->getHash()) {
                                return FALSE;
                            }
                        }
                        return TRUE;
                    },
                    ARRAY_FILTER_USE_BOTH
                );
            }
            ?>
            <?php foreach ($recordings as $recording) : ?>
                <tr class="<?php phe($recording->getClass()) ?>">
                    <td><?php phe(date('Y-m-d H:i', $recording->getStartTimestamp())) ?></td>
                    <td><?php phe($recording->getDurationAsString()) ?></td>
                    <td><?php phe($recording->getChannel()) ?></td>
                    <td><?php phe($recording->getName()) ?></td>
                    <td><?php phe($recording->getStatus()) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>

<?php require 'foot.inc.php' ?>
