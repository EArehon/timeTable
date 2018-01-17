<?php 
    require 'db.php';

    $data = $_POST;

    if(isset($data['addTime'])){
        $schedule = R::dispense('schedule');

        $schedule->date = $data['dateTime'];
        $schedule->lecturer = $data['FIO'];
        $schedule->group = $data['group'];
        $schedule->subject = $data['subject'];
        $schedule->id_room = $data['room'];

        R::store($schedule);

        header("Location: ".$_SERVER['PHP_SELF'].'?room='.$data['room']);
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="style.css" rel="stylesheet" type="text/css">
    <title>Главная страница</title>

    <script type="text/javascript">
            function showModalWin(date, time) {
 
                var darkLayer = document.createElement('div'); // слой затемнения
                darkLayer.id = 'shadow'; // id чтобы подхватить стиль
                document.body.appendChild(darkLayer); // включаем затемнение
 
                var modalWin = document.getElementById('popupWin'); // находим наше "окно"
                modalWin.style.display = 'block'; // "включаем" его
                document.getElementById('dateTime').value =  date + " " + time;

 
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

                
                //выводим корпуса и аудитории
                $corps =  R::getAll( 'SELECT * FROM corps' );

                foreach($corps as $corp){
                    echo $corp['name'].'<br>';
                    $classRoom = R::find('classroom',' id_corps LIKE ?', array($corp['id']));
                    foreach($classRoom as $room){
                        echo '<a href="index.php?room='.$room['id'].'">'.$room['room'].'</a> ';
                    }
                    echo '<br>';
                }


                //создаем массив с днями недели и расписанием звонков
                $time = array(0=>"08:30",1=>"10:05",2=>"11:55",3=>"13:40",4=>"15:00",5=>"16:30",6=>"18:00",7=>"19:30");
                $timeLength = count($time);
                $day = array(0=>"Понедельник",1=>"Вторник",2=>"Среда",3=>"Четверг",4=>"Пятница",5=>"Суббота");

               
               
               
               
                //определяем дату понедельника текущей недели
                if(date("w")!=1){
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

                //проверяем по какой аудитории будем выбирать расписание
                if(!isset($_GET['room'])){
                    $idRoom = 1;
                }
                else{
                    $idRoom = $_GET['room'];
                }
                
                //загружаем с базы расписание
                $timeTable = array();
                for($i = 0; $i < 6; $i++)
                {   
                    //$sql = 'SELECT * FROM schedule WHERE id_room=\''.$idRoom.'\' AND date LIKE \''.$date[$i].'%\'';
                    //needles = R::getAll('SELECT * FROM schedule WHERE id_room=\''.$idRoom.'\' AND date LIKE \''.$date[$i].'%\'');
                    //$needles =  R::getAll( 'SELECT * FROM schedule WHERE id_room='.$idRoom);
                    //$needles = R::find('schedule',' date LIKE ?', array($date[$i].'%'));
                    $needles = R::find('schedule',' id_room=\''.$idRoom.'\' AND date LIKE ?', array($date[$i].'%'));
                    //dump($needles);
                    //echo $sql.'<br>';
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
                                echo '<td class="columDate">'.$day[$i].'<br>('.$date[$i].')</td>';
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
                <H2 class="textLeft">Бронирование аудитории</H3>
                <hr>
                <form action="<?php echo $_SERVER["PHP_SELF"]?>" name="timeAddForm" method="POST">
                    <div class="tableForm">
                        <div class="tableFormRow">
                            <div class="tableFormCell"><label for="FIO">ФИО</label> </div>
                            <div class="tableFormCell"><input type="text" class="inputText" id="FIO" name="FIO" placeholder="ФИО преподавателя" required></div>
                        </div>
                        <div class="tableFormRow">
                            <div class="tableFormCell"> <label for="group">Группа</label> </div>
                            <div class="tableFormCell"><input type="text" class="inputText" id="group" name="group" placeholder="Учебная группа" required></div>
                        </div>
                        <div class="tableFormRow">
                            <div class="tableFormCell"> <label for="subject">Дисциплина</label> </div>
                            <div class="tableFormCell"><input type="text" class="inputText" id="subject" name="subject" placeholder="Дисциплина" required></div>
                        </div>
                        <div class="tableFormRow">
                            <div class="tableFormCell"> <label for="dateTime">Время</label> </div>
                            <div class="tableFormCell "><input type="text" class="inputText" id="dateTime" name="dateTime" readonly></div>
                        </div>
                        <div class="tableFormRow">
                            <div class="tableFormCell"></div>
                            <div class="tableFormCell"><input type="submit" value="Добавить" name="addTime"></div>
                        </div>
                    </div>
                    <input type="hidden" name="room" value="<?php echo $idRoom;?>">
                </form>
            </div>

        </main>
    </div>
</body>
</html>