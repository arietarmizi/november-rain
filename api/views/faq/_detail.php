<?php

use api\assets\AppAsset;
use yii\bootstrap\BootstrapPluginAsset;

AppAsset::register($this);
$this->beginPage();
?>
    <div class="container-fluid">
        <div class="accordion-option text-center">
            <h3 class="title "><?= $title ?></h3>
        </div>
        <div class="clearfix"></div>
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <?php
            

            foreach ($faq as $values) {
                $id = $values['id'];
                $title = $values['question'];
                $description = $values['answer'];
                $a = $values['id'];
                
                ?>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#panel<?= $id ?>"
                               aria-expanded="true"
                               aria-controls="collapseOne">
                                Q: <?= $title ?>
                            </a>
                        </h4>
                    </div>
                    <div id="panel<?= $id ?>" class="panel-collapse collapse" role="tabpanel">
                        <div class="panel-body">
                            A: <?= $description ?>
                        </div>
                    </div>
                </div>
                
                <?php
            } ?>
        </div>
    </div>
<?php
$this->endPage();