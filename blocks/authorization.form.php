<div class="divAut">
    <h2 class="textLeft">Авторизация</h2>
    <hr>

    <form action="index.php?action=authorization" method="POST">
        <div class="tableForm">
            <div class="tableFormRow">
                <div class="tableFormCell"><label for="login">Логин</label> </div>
                <div class="tableFormCell"><input type="text" class="inputText" id="login" name="login" placeholder="Имя пользователя" required></div>
            </div>
            <div class="tableFormRow">
                <div class="tableFormCell"> <label for="password">Пароль</label> </div>
                <div class="tableFormCell"><input type="text" class="inputText" id="password" name="password" placeholder="Пароль от учетной записи" required></div>
            </div>
            <div class="tableFormRow">
                <div class="tableFormCell"></div>
                <div class="tableFormCell"><input type="submit" class="btn" value="Войти" name="signIn"></div>
            </div>
        </div>
    </form>
</div>