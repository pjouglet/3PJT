package com.example.supinfo.traincommander;

import android.content.Context;
import android.content.SharedPreferences;
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

public class HomeActivity extends Activity {

    Button searchButton, historyButton, logOffButton;

    private View.OnClickListener searchListener = new View.OnClickListener(){
        @Override
        public void onClick(View v){
            Intent i = new Intent(HomeActivity.this, SearchTrainActivity.class);
            startActivity(i);
        }
    };

    private View.OnClickListener logOffListener = new View.OnClickListener() {
        @Override
        public void onClick(View v) {
            SharedPreferences sharedPref = getSharedPreferences("pref",Context.MODE_PRIVATE);
            Log.i("log debug from Home", sharedPref.getString("logMethod", "nothing"));
            Log.i("log debug", sharedPref.getString("logID", "nothing"));
            SharedPreferences.Editor editor = sharedPref.edit();
            editor.putString("logMethod", "nothing");
            editor.putString("logID", "nothing");
            editor.commit();

            Intent i = new Intent(HomeActivity.this, MainActivity.class);
            startActivity(i);
        }
    };

    private View.OnClickListener historyListener = new View.OnClickListener(){
        @Override
        public void onClick(View v){
            SharedPreferences sharedPref = getSharedPreferences("pref", Context.MODE_PRIVATE);
            String id = sharedPref.getString("logID", "");
            String url = "http://api.train-commander.fr/history/"+id;
            Intent i = new Intent(HomeActivity.this, HistoryActivity.class);
            i.putExtra("url", url);
            startActivity(i);
        }
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        this.requestWindowFeature(Window.FEATURE_NO_TITLE);

        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_home);

        searchButton = (Button) findViewById(R.id.buttonSearch);
        searchButton.setText("Search a trip");
        searchButton.setOnClickListener(searchListener);

        historyButton = (Button) findViewById(R.id.buttonHistory);
        historyButton.setText("See history");
        historyButton.setOnClickListener(historyListener);

        logOffButton = (Button) findViewById(R.id.buttonLogOff);
        logOffButton.setOnClickListener(logOffListener);
    }


}
