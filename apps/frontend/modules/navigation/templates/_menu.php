<ul>
                    <li<?php if ($current == 'project') echo " class='current'" ?>><a href="<?php echo url_for('project/index') ?>">Projects</a></li>
                    <li<?php if ($current == 'mining') echo " class='current'" ?>><a href="<?php echo url_for('mining/index') ?>">Minings</a></li>
                    <li<?php if ($current == 'chaining') echo " class='current'" ?>><a href="<?php echo url_for('chaining/index') ?>">Chainings</a></li>
                    <li<?php if ($current == 'viz') echo " class='current'" ?>><a href="<?php echo url_for('vis/index') ?>">Visualizations</a></li>
                </ul>
