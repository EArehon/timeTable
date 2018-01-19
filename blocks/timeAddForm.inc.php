            <div style="text-align: center" id="popupWin" class="modalwin">
                <H2 class="textLeft">Бронирование аудитории</H2>
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
                            <div class="tableFormCell"><input type="submit" class="btn" value="Добавить" name="addTime"></div>
                        </div>
                    </div>
                    <input type="hidden" name="room" value="<?php echo $idRoom;?>">
                </form>
            </div>