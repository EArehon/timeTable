<?php 
    require 'db.php';
    include('blocks/functions.inc.php');

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

        <nav>
            <?php
                printMenu();

                printMenuAut();
            ?>
        </nav>

        <main>
            <?php  
                if(isset($_SESSION['logged_user'])){
                    echo 'Авторизация выполнена. Пользователь: '.$_SESSION['logged_user']->login;
                }
                else{
                    echo "Вход не выполнен.";
                }

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
</body>
</html>