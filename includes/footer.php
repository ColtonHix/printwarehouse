<div class="footer">
    <div>
        &copy; copyright 2021 Colton Hix all rights reserved.
    </div>
    <div>
        This is other info, as well as an email address: <a href="mailto:coltonhix@u.boisestate.edu">Contact Me</a>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="loginModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginTitle">Login</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="loginForm" novalidate>
                    <div class="form-group">
                        <label for="loginUsername">Username:</label>
                        <input class="form-control" type="text" id="loginUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="loginPassword">Password:</label>
                        <input class="form-control" type="password" id="loginPassword" name="password" required>
                    </div>
                </form>
                <form id="registerForm" class="hidden" novalidate="true">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input class="form-control" type="text" id="username" name="username" required pattern="^[a-zA-Z0-9\\-\\_]{3,25}$">
                        <div class="invalid-feedback">
                            Username must be between 3 and 25 characters and may only include letters, numbers, dashes and underscores.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input class="form-control" type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input class="form-control" type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="password2">Confirm Password:</label>
                        <input class="form-control" type="password" id="password2" required>
                    </div>    
                </form>
                <div id="registerErrors" class="hidden error-message"></div>
                <span id="toRegister">Don't have an account? <a href="#" onClick="showRegistration()">Create one today.</a></span>
                <span id="toLogin" class="hidden"><a href="#" onClick="showLogin()">Back to Login</a></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="loginSubmit" onClick="validateLogin()">Login</button>
            </div>
        </div>
    </div>
</div>
</body>
<script src="/includes/js/global.js"></script>