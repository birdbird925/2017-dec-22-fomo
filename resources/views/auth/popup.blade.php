<div class="login-popup">
    <div class="close-nav login-tab"></div>
    <div class="popup-inner">
        <div class="col-sm-6 login mobile">
            <div class="title">SIGN IN</div>
            <form action="/login" method="post">
                {{ csrf_field() }}
                <div class="form-group">
                    <label>Your email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <input type="submit" value="Log Me In">
            </form>
        </div>
        <div class="col-sm-6 register">
            <div class="title">SIGN UP</div>
            <form action="/register" method="post">
                {{ csrf_field() }}
                <div class="form-group">
                    <label>Your email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <input type="submit" value="Continue">
            </form>
        </div>
    </div>
    <div class="popup-footer">
        <div class="col-sm-6 social-login-box">
            <hr>
            <label>Or continue with</label>
            <a href="/login/facebook?backto={{Request::url()}}" class="facebook social-login">Facebook</a>
            <a href="/login/google?backto={{Request::url()}}" class="google social-login">Google</a>
        </div>
        <div class="col-sm-6">
            <hr>
            <div class="action-switcher">
                <label>Don't have account?</label>
                <a class="switch">Sign up now!</a>
            </div>
            <a href="/password/reset" class="forget-password">Forget Password</a>
        </div>
    </div>
</div>
