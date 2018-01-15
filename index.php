<?php 
    require 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="style.css" rel="stylesheet" type="text/css">
    <title>Главная страница</title>

    <script type="text/javascript">
            function showModalWin(a, b) {
                //alert(a + " " + b);
 
                var darkLayer = document.createElement('div'); // слой затемнения
                darkLayer.id = 'shadow'; // id чтобы подхватить стиль
                document.body.appendChild(darkLayer); // включаем затемнение
 
                var modalWin = document.getElementById('popupWin'); // находим наше "окно"
                modalWin.style.display = 'block'; // "включаем" его
                document.getElementById('atata').value = a + " " + b;

                darkLayer.onclick = function () {  // при клике на слой затемнения все исчезнет
                    darkLayer.parentNode.removeChild(darkLayer); // удаляем затемнение
                    modalWin.style.display = 'none'; // делаем окно невидимым
                    return false;
                };
            }
    </script>
</head>
<body>
    <div id="wrapper">
        
        <header>
        
        </header>

        <main>
            <?php  
                function dump($what){
                    echo '<pre>';print_r($what);echo '</pre>';
                }

                //setlocale(LC_ALL, 'rus_RUS');123
                $time = array(0=>"08:30",1=>"10:05",2=>"11:55",3=>"13:40",4=>"15:00",5=>"16:30",6=>"18:00",7=>"19:30");
                $timeLength = count($time);
                $day = array(0=>"Понедельник",1=>"Вторник",2=>"Среда",3=>"Четверг",4=>"Пятница",5=>"Суббота");

               

                //определяем дату понедельника текущей недели
                if(date("w") != 1){
                    $monday = (string) date("Y-m-d", strtotime("last Monday"));
                }
                else{
                    $monday = (string) date("Y-m-d");
                }
                
                $dates = new DateTime($monday);
                
                //создаем массив с датами занятий
                $date = array();
                for($i = 0; $i<6; $i++){
                    $date[$i] = (string) $dates->format('Y-m-d');
                    $dates->add(new DateInterval('P1D'));
                }

                //загружаем с базы расписание
                $timeTable = array();
                for($i = 0; $i < 6; $i++)
                {
                    $needles = R::find('schedule',' date LIKE ?', array($date[$i].'%'));

                    foreach($needles as $nedl)
                    {
                        //проверяем на какое время записано занятие и ищем индекс в массиве с расписанием звонков
                        $idTime = array_search(date("H:i", strtotime($nedl->date)), $time);

                        //записываем информацию о занятии
                        $timeTable[$idTime][$i] = $nedl;
                    }
                }
            ?>
            
            <h2>Текущая неделя</h2>

            <table class="timeTable">
                <thead>
                    <tr>
                        <td>&nbsp;</td>
                        <?php
                            for($i = 0; $i < 6; $i++){
                                echo '<td>'.$day[$i].'<br>('.$date[$i].')</td>';
                            }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        for ($i = 0; $i < $timeLength; $i++) {
                            echo '<tr>';
                            echo '<td>'.$time[$i].'</td>';
                            
                            for($j = 0; $j < 6; $j++)
                            {
                                if($timeTable[$i][$j] != null)
                                {
                                    echo '<td>';
                                    echo $timeTable[$i][$j]->lecturer.'<br>';
                                    echo $timeTable[$i][$j]->group.'<br>';
                                    echo $timeTable[$i][$j]->subject;
                                    echo '</td>';
                                }
                                else
                                {
                                    //echo '<td class="empty" ondblclick="alert(\''.$date[$j].' '.$time[$i].'\')">';
                                    echo '<td class="empty" ondblclick="showModalWin(\''.$date[$j].'\', \''.$time[$i].'\')">';
                                    echo '</td>';
                                }
                                

                            }
                            
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>

            <div style="text-align: center" id="popupWin" class="modalwin">
                <h2 id="tratata"> Какая-то форма </h2>
                <form>
                    <input type="text" value="" id="atata">
                    <input type="button" value="OK">
                </form>
                <hr>
                <h2> Какой-то текст </h2>
                <br> <p> УРа!!!!!!!!!! </p>
                <hr>
            </div>

        </main>
    </div>
</body>
</html>