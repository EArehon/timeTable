<?php 
    require 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="style.css" rel="stylesheet" type="text/css">
    <title>Главная страница</title>
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

                //setlocale(LC_ALL, 'rus_RUS');
                $time = array(0=>"08:30",1=>"10:05",2=>"11:55",3=>"13:40",4=>"15:00",5=>"16:30",6=>"18:00",7=>"19:30");
                $timeLength = count($time);

                

                $dates = new DateTime('2018-01-08');
                
                //создаем массив с датами занятий
                $date = array();
                for($i = 0; $i<6; $i++){
                    $date[$i] = (string) $dates->format('Y-m-d');
                    $dates->add(new DateInterval('P1D'));
                }

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
                        <td>Понедельник</td>
                        <td>Вторник</td>
                        <td>Среда</td>
                        <td>Четверг</td>
                        <td>Пятница</td>
                        <td>Суббота</td> 
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
                                    //echo '<td class="empty" ondblclick="alert(\'Клик!\')">';
                                    echo '<td class="empty" ondblclick="alert(\''.$date[$j].' '.$time[$i].'\')">';
                                        //echo 'yps';   onclick="alert('Клик!')"
                                    echo '</td>';
                                }
                                

                            }
                            
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>