<?php 
    require 'db.php';
    include('blocks/functions.inc.php');

    $data = $_POST;

    //добавление записи расписания
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

    //удаление записи расписания
    if(isset($data['deleteTime'])){
        R::exec('DELETE FROM schedule WHERE id='.$data['idTime']);

        header("Location: ".$_SERVER['PHP_SELF'].'?room='.$data['room']);
    }

    //изменение записи расписания
    if(isset($data['EditTime'])){
        $schedul = R::load('schedule', $data['idEdit']);

        $schedul->date = $data['dateTimeEdit'];
        $schedul->lecturer = $data['FIOEdit'];
        $schedul->group = $data['groupEdit'];
        $schedul->subject = $data['subjectEdit'];
        $schedul->id_room = $data['room'];
        
        R::store($schedul);
        
        header("Location: ".$_SERVER['PHP_SELF'].'?room='.$data['room']);
    }

    
    if( isset($data['signIn']) or isset($data['signOut'])){
        include('blocks/authorization.inc.php');
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <title>Расписание компьютерных лабораторий БрГУ</title>

    
</head>
<body>
    <div id="wrapper">
        
        <header>

        </header>

        <nav>
            <?php
                printMenu();

                printMenuAut();
            ?>
        </nav>

        <main>
            <?php  
               if(isset($_GET['action'])){
                    include('blocks/authorization.form.php');
                }
                else{
                    //создаем массив с днями недели и расписанием звонков
                    $time = array(0=>"08:30",1=>"10:05",2=>"11:55",3=>"13:40",4=>"15:00",5=>"16:30",6=>"18:00",7=>"19:30");
                    $timeLength = count($time);
                    $day = array(0=>"Понедельник",1=>"Вторник",2=>"Среда",3=>"Четверг",4=>"Пятница",5=>"Суббота");
                    
                    //проверяем по какой аудитории будем выбирать расписание
                    if(!isset($_GET['room'])){
                        $idRoom = 1;
                    }
                    else{
                        $idRoom = $_GET['room'];
                    }

                    //определяем дату понедельника текущей недели
                    if(date("w")!=1){
                        $monday = (string) date("Y-m-d", strtotime("last Monday"));
                    }
                    else{
                        $monday = (string) date("Y-m-d");
                    }
                    $date = new DateTime($monday);


                    print "<h2>Текущая неделя</h2>";
                    shedule($idRoom, $date);

                    //$nextWeek = new DateTime($monday);
                    //$nextWeek->add(new DateInterval('P7D'));
                    //print "<h2>Следующая неделя</h2>";
                    //shedule($idRoom, $nextWeek);
                }
            ?>
        </main>
    </div>

    <script type="text/javascript">
            
            
            function showEditWin(idS, dateS, lecturerS, groupS, subjectS) {
                var darkLayer = document.createElement('div'); // слой затемнения
                darkLayer.id = 'shadow'; // id чтобы подхватить стиль
                document.body.appendChild(darkLayer); // включаем затемнение
 
                var modalWin = document.getElementById('editWin'); // находим наше "окно"
                modalWin.style.display = 'block'; // "включаем" его
                document.getElementById('idEdit').value =  idS;
                document.getElementById('dateTimeEdit').value =  dateS;
                document.getElementById('FIOEdit').value =  lecturerS;
                document.getElementById('groupEdit').value =  groupS;
                document.getElementById('subjectEdit').value =  subjectS;idEdit

                darkLayer.onclick = function () {  // при клике на слой затемнения все исчезнет
                    darkLayer.parentNode.removeChild(darkLayer); // удаляем затемнение
                    modalWin.style.display = 'none'; // делаем окно невидимым
                    return false;
                };
            }
            
            function showDeletelWin(idTime) {
 
                var darkLayer = document.createElement('div'); // слой затемнения
                darkLayer.id = 'shadow'; // id чтобы подхватить стиль
                document.body.appendChild(darkLayer); // включаем затемнение
 
                var modalWin = document.getElementById('deleteWin'); // находим наше "окно"
                modalWin.style.display = 'block'; // "включаем" его
                document.getElementById('idTime').value =  idTime;

                darkLayer.onclick = function () {  // при клике на слой затемнения все исчезнет
                    darkLayer.parentNode.removeChild(darkLayer); // удаляем затемнение
                    modalWin.style.display = 'none'; // делаем окно невидимым
                    return false;
                };

                var btnNo = document.getElementById('Noo');
                btnNo.onclick = function () {  // при клике на слой затемнения все исчезнет
                    darkLayer.parentNode.removeChild(darkLayer); // удаляем затемнение
                    modalWin.style.display = 'none'; // делаем окно невидимым
                    return false;
                };
            }
            
            
            
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

            var flag = false;

            function showLayer(typeLayer){
                if(flag){
                    document.getElementById('btnCancel').click();
                }

                var Layers = timeTable.getElementsByClassName(typeLayer);
                var LayersLength = Layers.length;

                for(var i=0; i < LayersLength; i++){
                    Layers[i].style.display = 'block';
                }
                var cancl = document.getElementById('btnCancel');
                cancl.setAttribute('onclick', 'cancel(\''+typeLayer+'\');');

                flag = true;
            }

            function cancel(typeLayer){
                var Layers = timeTable.getElementsByClassName(typeLayer);
                var LayersLength = Layers.length;

                for(var i=0; i < LayersLength; i++){
                    Layers[i].style.display = 'none';
                }
            }
    </script>

</body>
</html>