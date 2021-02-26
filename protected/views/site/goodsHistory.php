
<?php if (!empty($model->attr)): ?>
    <?php
        foreach ($model->attr as $goodsList){
            echo '<div class="goodsHistoryDiv divHistory'.$goodsList["id"].'">';
            echo '<table class="table table-bordered table-striped table-hover" style="margin-bottom: 0px;">';
            echo '<thead>';
            echo '<tr><th>领货日期</th><th>领货同事</th><th>领货数量</th></tr>';
            echo '</thead>';
            echo '<tbody>';
            if (empty($goodsList['goodsHistory'])){
                echo "<tr><td colspan='3' class='text-center'>沒有记录</td></tr>";
            }else{
                foreach ($goodsList['goodsHistory'] as $historyList){
                    echo "<tr>";
                    echo "<td>".$historyList["lud"]."</td>";
                    echo "<td>".$historyList["lcu"]."</td>";
                    echo "<td>".$historyList["confirm_num"]."</td>";
                    echo "</tr>";
                }
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }
    ?>
<?php endif ?>