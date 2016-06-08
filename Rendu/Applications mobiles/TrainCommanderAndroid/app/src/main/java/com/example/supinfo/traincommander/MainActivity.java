package com.example.supinfo.traincommander;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.SharedPreferences;
import android.support.v4.app.FragmentActivity;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import android.app.Activity;
import android.content.Intent;

import com.facebook.AccessToken;
import com.facebook.CallbackManager;
import com.facebook.FacebookCallback;
import com.facebook.FacebookException;
import com.facebook.Profile;
import com.facebook.ProfileTracker;
import com.facebook.login.LoginManager;
import com.facebook.login.LoginResult;
import com.facebook.login.widget.LoginButton;
import com.google.android.gms.auth.api.Auth;
import com.google.android.gms.auth.api.signin.GoogleSignInAccount;
import com.google.android.gms.auth.api.signin.GoogleSignInOptions;
import com.google.android.gms.auth.api.signin.GoogleSignInResult;
import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.api.GoogleApiClient;

import com.facebook.FacebookSdk;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Date;
import java.util.concurrent.ExecutionException;

public class MainActivity extends FragmentActivity {

    TextView title;
    EditText login, password;
    Button loginButton, signButton;

    GoogleApiClient mGoogleApiClient;
    private static final int RC_SIGN_IN = 9001;

    LoginButton facebookButton;
    CallbackManager callbackManager;
    private ProfileTracker profileTracker;

    private View.OnClickListener loginListener = new View.OnClickListener(){
        @Override
        public void onClick(View v){
            boolean fail = true;
            if (!login.getText().toString().equals("") && !password.getText().toString().equals("")){
                String split[] = login.getText().toString().split("@");
                Log.i("debug", Integer.toString(split.length));
                if (split.length != 1){
                    if (!split[0].equals("") && !split[1].equals("")){
                        String email = login.getText().toString().replace(" ", "");
                        email = email.toLowerCase();
                        String pass = password.getText().toString().replace(" ", "");
                        String url = "http://api.train-commander.fr/connect/"+email+"/"+pass;
                        Connect connect = new Connect();

                        try {
                            JSONObject jo = connect.execute(url).get();
                            String id = jo.getString("id");
                            Log.i("debug connect", id);

                            if (!id.equals("0")){
                                SharedPreferences sharedPref = getSharedPreferences("pref", Context.MODE_PRIVATE);
                                SharedPreferences.Editor editor = sharedPref.edit();
                                editor.putString("logMethod", "Account");
                                editor.putString("logID", id);
                                editor.commit();
                                Intent i = new Intent(MainActivity.this, HomeActivity.class);
                                startActivity(i);
                                fail = false;
                            }

                        } catch (InterruptedException e) {
                            e.printStackTrace();
                        } catch (ExecutionException e) {
                            e.printStackTrace();
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                    }

                }
            }
            if (fail){
                AlertDialog.Builder builder = new AlertDialog.Builder(MainActivity.this);
                builder.setMessage("Failed to connect !")
                        .setPositiveButton("Ok", new DialogInterface.OnClickListener() {
                            public void onClick(DialogInterface dialog, int id) {
                            }
                        });
                builder.create();
                builder.show();
            }

        }
    };

    private View.OnClickListener signListener = new View.OnClickListener() {
        @Override
        public void onClick(View v){
            Intent i = new Intent(MainActivity.this, CreateAccountActivity.class);
            startActivity(i);
        }
    };

    private View.OnClickListener googleListener = new View.OnClickListener() {
        @Override
        public void onClick(View v) {
            switch (v.getId()) {
                case R.id.sign_in_button:
                    signIn();
                    break;
            }
        }
    };

    private GoogleApiClient.OnConnectionFailedListener failedListener = new GoogleApiClient.OnConnectionFailedListener() {
        @Override
        public void onConnectionFailed(ConnectionResult connectionResult) {

        }
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        this.requestWindowFeature(Window.FEATURE_NO_TITLE);

        SharedPreferences sharedPref = getSharedPreferences("pref", Context.MODE_PRIVATE);
        if (!sharedPref.getString("logMethod", "nothing").equals("nothing")){
            if (!sharedPref.getString("logID", "nothing").equals("nothing")){
                Intent i = new Intent(MainActivity.this, HomeActivity.class);
                startActivity(i);
            }
        }

        Log.i("log debug", sharedPref.getString("logMethod", "nothing"));
        Log.i("log debug", sharedPref.getString("logID", "nothing"));

        super.onCreate(savedInstanceState);

        FacebookSdk.sdkInitialize(getApplicationContext());

        setContentView(R.layout.activity_main);

        title = (TextView) findViewById(R.id.textView);
        title.setText("Train-Commander");

        login = (EditText) findViewById(R.id.login);
        login.setHint("email");

        password = (EditText) findViewById(R.id.password);
        password.setHint("password");

        loginButton = (Button) findViewById(R.id.button);
        loginButton.setText("login");
        loginButton.setOnClickListener(loginListener);

        signButton = (Button) findViewById(R.id.signin);
        signButton.setOnClickListener(signListener);

        // Google
        GoogleSignInOptions gso = new GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
                .requestEmail()
                .build();

        mGoogleApiClient = new GoogleApiClient.Builder(this)
                .enableAutoManage(this, failedListener)
                .addApi(Auth.GOOGLE_SIGN_IN_API, gso)
                .build();

        findViewById(R.id.sign_in_button).setOnClickListener(googleListener);

        // Facebook

        callbackManager = CallbackManager.Factory.create();
        facebookButton = (LoginButton) findViewById(R.id.login_button);
        facebookButton.setReadPermissions("public_profile");

        facebookButton.registerCallback(callbackManager, new FacebookCallback<LoginResult>() {
            @Override
            public void onSuccess(LoginResult loginResult) {
                // App code
                Log.i("debug", "Facebook log-in success");
                Profile profile = Profile.getCurrentProfile();
                String url = "http://api.train-commander.fr/user/fb/"+profile.getId();
                Connect retrieveJson = new Connect();
                try {
                    JSONObject jo = retrieveJson.execute(url).get();
                    String id = jo.getString("id");
                    if (id.equals("0")){
                        String url2 = "http://api.train-commander.fr/create/user/fb/"+profile.getFirstName()+"/"+profile.getLastName()+"/"+profile.getId();
                        Connect create = new Connect();
                        try {
                            JSONObject j = create.execute(url2).get();
                            SharedPreferences sharedPref = getSharedPreferences("pref", Context.MODE_PRIVATE);
                            SharedPreferences.Editor editor = sharedPref.edit();
                            editor.putString("logMethod", "Facebook");
                            editor.putString("logID", j.getString("id"));
                            editor.commit();
                            Intent i = new Intent(MainActivity.this, HomeActivity.class);
                            startActivity(i);
                        } catch (InterruptedException e) {
                            e.printStackTrace();
                        } catch (ExecutionException e) {
                            e.printStackTrace();
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                    }
                    else {
                        SharedPreferences sharedPref = getSharedPreferences("pref", Context.MODE_PRIVATE);
                        SharedPreferences.Editor editor = sharedPref.edit();
                        editor.putString("logMethod", "Facebook");
                        editor.putString("logID", id);
                        editor.commit();
                        Intent i = new Intent(MainActivity.this, HomeActivity.class);
                        startActivity(i);
                    }
                } catch (InterruptedException e) {
                    e.printStackTrace();
                } catch (ExecutionException e) {
                    e.printStackTrace();
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }

            @Override
            public void onCancel() {
                // App code
                Log.i("debug", "Facebook log-in canceled");
            }

            @Override
            public void onError(FacebookException exception) {
                // App code
                Log.i("debug", "Facebook log-in Error");
            }
        });

    }

    private void signIn() {
        Intent signInIntent = Auth.GoogleSignInApi.getSignInIntent(mGoogleApiClient);
        startActivityForResult(signInIntent, RC_SIGN_IN);
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        callbackManager.onActivityResult(requestCode, resultCode, data);
        // Result returned from launching the Intent from GoogleSignInApi.getSignInIntent(...);
        if (requestCode == RC_SIGN_IN) {
            GoogleSignInResult result = Auth.GoogleSignInApi.getSignInResultFromIntent(data);
            handleSignInResult(result);
        }
    }

    private void handleSignInResult(GoogleSignInResult result) {
        Log.d("Google sign-in", "handleSignInResult:" + result.isSuccess());
        if (result.isSuccess()) {
            // Signed in successfully, show authenticated UI.
            GoogleSignInAccount acct = result.getSignInAccount();
            Log.i("Google name",acct.getDisplayName());

            String url = "http://api.train-commander.fr/user/google/"+acct.getId();
            Connect retrieveJson = new Connect();
            try {
                JSONObject jo = retrieveJson.execute(url).get();
                String id = jo.getString("id");
                if (id.equals("0")){
                    String[] split = acct.getDisplayName().split(" ",2);
                    String url2 = "http://api.train-commander.fr/create/user/google/"+split[0]+"/"+split[1]+"/"+acct.getId();
                    Connect create = new Connect();
                    try {
                        JSONObject j = create.execute(url2).get();
                        SharedPreferences sharedPref = getSharedPreferences("pref", Context.MODE_PRIVATE);
                        SharedPreferences.Editor editor = sharedPref.edit();
                        editor.putString("logMethod", "Google");
                        editor.putString("logID", j.getString("id"));
                        editor.commit();
                        Intent i = new Intent(MainActivity.this, HomeActivity.class);
                        startActivity(i);
                    } catch (InterruptedException e) {
                        e.printStackTrace();
                    } catch (ExecutionException e) {
                        e.printStackTrace();
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }
                else {
                    SharedPreferences sharedPref = getSharedPreferences("pref", Context.MODE_PRIVATE);
                    SharedPreferences.Editor editor = sharedPref.edit();
                    editor.putString("logMethod", "Google");
                    editor.putString("logID", id);
                    editor.commit();
                    Intent i = new Intent(MainActivity.this, HomeActivity.class);
                    startActivity(i);
                }
            } catch (InterruptedException e) {
                e.printStackTrace();
            } catch (ExecutionException e) {
                e.printStackTrace();
            } catch (JSONException e) {
                e.printStackTrace();
            }

            /*final SharedPreferences sharedPref = getSharedPreferences("pref", Context.MODE_PRIVATE);
            SharedPreferences.Editor editor = sharedPref.edit();
            editor.putString("logMethod", "Google");
            Log.i("Google account", acct.getId());
            editor.putString("logID", acct.getId());
            editor.commit();
            Intent i = new Intent(MainActivity.this, HomeActivity.class);
            startActivity(i);*/
        } else {
            Log.i("Debug", "failed log-in Google");
        }
    }

}
