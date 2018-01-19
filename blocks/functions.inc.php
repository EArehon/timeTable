<?php 
    function dump($what){
        echo '<pre>';print_r($what);echo '</pre>';
    }

    //формируем меню: корпус -> аудитория
    function printMenu(){
        $corps =  R::getAll( 'SELECT * FROM corps' );
        echo '<ul>';
        foreach($corps as $corp){
            echo '<li><a href="#">'.$corp['name'].'</a>';
            echo '<ul>';
            $classRoom = R::find('classroom',' id_corps LIKE ?', array($corp['id']));
            foreach($classRoom as $room){
                echo '<li><a href="index.php?room='.$room['id'].'">'.$room['room'].'</a></li>';
            }
            echo '</ul>';
            echo '</li>';
        }
        echo '</ul>';
    }

    function printMenuAut(){
        echo '<ul id="authorization">';
        if(isset($_SESSION['logged_user'])){
            echo '<li><a href="index.php?action=authorization">Выйти</a></li>';
        }    
        else{
            echo '<li><a href="index.php?action=authorization">Войти</a></li>';
            //echo '<li><form action="index.php?action=authorization" method="POST">';
            //echo '<input type="submit" name="signOut" value="Выйти" class="signOut">';
            //echo '<form></li>';
        }
        echo '</ul>';
    }

    //вывод таблицы с расписанием
    function shedule($idRoom, $dateMonday){
        global $time;
        global $timeLength;
        global $day;
        
        //создаем массив с датами занятий
        $date = array();
        for($i = 0; $i<6; $i++){
            $date[$i] = (string) $dateMonday->format('Y-m-d');
            $dateMonday->add(new DateInterval('P1D'));
        }

        //загружаем с базы расписание
        $timeTable = array();
        for($i = 0; $i < 6; $i++)
        {   
            $needles = R::find('schedule',' id_room=\''.$idRoom.'\' AND date LIKE ?', array($date[$i].'%'));

            foreach($needles as $nedl){
                //проверяем на какое время записано занятие и ищем индекс в массиве с расписанием звонков
                $idTime = array_search(date("H:i", strtotime($nedl->date)), $time);

                //записываем информацию о занятии
                $timeTable[$idTime][$i] = $nedl;
            }
        }

        print "
            <table class=\"timeTable\">
            <thead>
                <tr>
                    <td>&nbsp;</td>
        ";

        for($i = 0; $i < 6; $i++){
            echo '<td class="columDate">'.$day[$i].'<br>('.$date[$i].')</td>';
        }

        print "
                    </tr>
                </thead>
            <tbody>
        ";

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

        print "
            </tbody>
            </table>
        ";

        include('blocks/timeAddForm.inc.php');
    }
?>