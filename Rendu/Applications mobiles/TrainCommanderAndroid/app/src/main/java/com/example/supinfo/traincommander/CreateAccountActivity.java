package com.example.supinfo.traincommander;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.app.Activity;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.concurrent.ExecutionException;

public class CreateAccountActivity extends Activity {
    EditText firstname, lastname, email, password;
    CheckBox newsletters;
    Button signin;

    private View.OnClickListener signinListener = new View.OnClickListener() {
        @Override
        public void onClick(View v) {
            if (firstname.getText().length() != 0 && lastname.getText().length()!= 0 && email.getText().length() != 0 && password.length() != 0){
                String url = "http://api.train-commander.fr/create/user/"+firstname.getText().toString()+"/"+lastname.getText().toString()+"/"+password.getText().toString()+"/"+email.getText().toString()+"/";
                if (newsletters.isChecked()){
                    url += "1";
                }
                else {
                    url += "0";
                }
                Connect connect = new Connect();

                try {
                    JSONObject jo = connect.execute(url).get();
                    String id = jo.getString("id");

                    if (!id.equals("0")){
                        SharedPreferences sharedPref = getSharedPreferences("pref", Context.MODE_PRIVATE);
                        SharedPreferences.Editor editor = sharedPref.edit();
                        editor.putString("logMethod", "Account");
                        editor.putString("logID", id);
                        editor.commit();
                        Intent i = new Intent(CreateAccountActivity.this, HomeActivity.class);
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
        }
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        this.requestWindowFeature(Window.FEATURE_NO_TITLE);

        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_create_account);

        firstname = (EditText) findViewById(R.id.firstname);
        lastname = (EditText) findViewById(R.id.lastname);
        email = (EditText) findViewById(R.id.email);
        password = (EditText) findViewById(R.id.password);
        newsletters = (CheckBox) findViewById(R.id.checkBox);
        signin = (Button) findViewById(R.id.signin);
        signin.setOnClickListener(signinListener);

    }


}
