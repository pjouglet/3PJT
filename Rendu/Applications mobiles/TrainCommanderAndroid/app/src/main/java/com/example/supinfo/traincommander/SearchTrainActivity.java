package com.example.supinfo.traincommander;

import android.app.AlertDialog;
import android.app.TimePickerDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.Window;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.app.Activity;
import android.widget.Spinner;
import android.widget.TextView;
import android.view.View;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.widget.TimePicker;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.sql.Time;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.concurrent.ExecutionException;

public class SearchTrainActivity extends Activity {

    private Spinner departure, arrival, sort;
    private TextView dateView, before, after;
    private DatePicker datePicker;
    private Calendar calendar;
    private int year, month, day;
    private Button searchButton;

    ArrayList<Integer> listStationsId;

    private View.OnClickListener searchListener = new View.OnClickListener(){
        @Override
        public void onClick(View v){
            String sortType;
            if (sort.getSelectedItemPosition() == 0){
                sortType = "time";
            }
            else {
                sortType = "cost";
            }

            int idDeparture = listStationsId.get(departure.getSelectedItemPosition());
            int idArrival = listStationsId.get(arrival.getSelectedItemPosition());

            String[] split = after.getText().toString().split("H");
            SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy/hh/mm");
            try {
                Date after = sdf.parse(dateView.getText().toString()+"/"+split[0]+"/"+split[1]);
                split = before.getText().toString().split("H");
                Date before = sdf.parse(dateView.getText().toString()+"/"+split[0]+"/"+split[1]);

                String url;

                if (after.equals(before)){
                    url = "http://api.train-commander.fr/journeys/"+sortType+"/"+idDeparture+"/"+idArrival+"/"+after.getTime()/1000;
                }
                else {
                    url = "http://api.train-commander.fr/journeys/"+sortType+"/"+idDeparture+"/"+idArrival+"/"+after.getTime()/1000+"/"+before.getTime()/1000;
                }

                Log.i("url", url);
                Intent i = new Intent(SearchTrainActivity.this, AvailableTrainActivity.class);
                i.putExtra("url", url);
                startActivity(i);
            } catch (ParseException e) {
                e.printStackTrace();
            }
        }
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);

        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_search_train);

        ArrayList<String> listStations = new ArrayList<>();
        listStationsId = new ArrayList<>();

        // Load stations
        String url = "http://api.train-commander.fr/stations";
        RetrieveJson retrieveJson = new RetrieveJson();
        try {
            JSONArray ja = retrieveJson.execute(url).get();
            if (ja != null){
                for (int j = 0; j < ja.length(); j++)
                {
                    JSONObject jo = ja.getJSONObject(j);
                    listStations.add(jo.getString("name"));
                    listStationsId.add(jo.getInt("id"));
                }
            }
            else {
                AlertDialog.Builder builder = new AlertDialog.Builder(SearchTrainActivity.this);
                builder.setMessage("Failed to reach the server !")
                        .setPositiveButton("Ok", new DialogInterface.OnClickListener() {
                            public void onClick(DialogInterface dialog, int id) {
                                Intent i = new Intent(SearchTrainActivity.this, HomeActivity.class);
                                startActivity(i);
                            }
                        });
                builder.create();
                builder.show();
            }
        } catch (InterruptedException e) {
            e.printStackTrace();
        } catch (ExecutionException e) {
            e.printStackTrace();
        } catch (JSONException e) {
            e.printStackTrace();
        }

        departure = (Spinner) findViewById(R.id.departure);
        // Creating adapter for spinner
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(this, android.R.layout.simple_spinner_item, listStations);

        // Drop down layout style - list view with radio button
        dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);

        // attaching data adapter to spinner
        departure.setAdapter(dataAdapter);

        arrival = (Spinner) findViewById(R.id.arrival);
        arrival.setAdapter(dataAdapter);

        dateView = (TextView) findViewById(R.id.dateView);
        calendar = Calendar.getInstance();
        year = calendar.get(Calendar.YEAR);
        month = calendar.get(Calendar.MONTH);
        day = calendar.get(Calendar.DAY_OF_MONTH);
        showDate(year, month+1, day);

        before = (TextView) findViewById(R.id.before);
        showTimeBefore(0, 0);
        after = (TextView) findViewById(R.id.after);
        showTimeAfter(0, 0);

        searchButton = (Button) findViewById(R.id.button2);
        searchButton.setOnClickListener(searchListener);

        ArrayList<String> sortString = new ArrayList<>();
        sortString.add("time");
        sortString.add("price");
        sort = (Spinner) findViewById(R.id.sort);
        dataAdapter = new ArrayAdapter<String>(this, R.layout.spinnerlayout, sortString);
        sort.setAdapter(dataAdapter);

        Intent intent2 = getIntent();
        if (intent2.getExtras() != null){
            String startStation = intent2.getExtras().getString("startStation");
            String arrivalStation = intent2.getExtras().getString("arrivalStation");
            String startTime = intent2.getExtras().getString("startTime");
            int index = listStations.indexOf(startStation);
            departure.setSelection(index);
            index = listStations.indexOf(arrivalStation);
            arrival.setSelection(index);
            String[] split = startTime.split(" ");
            split = split[1].split(":");
            after.setText(split[0]+"H"+split[1]);
            before.setText(split[0]+"H"+split[1]);
        }

    }
    @SuppressWarnings("deprecation")
    public void setDate(View view) {
        showDialog(999);
    }
    @Override
    protected Dialog onCreateDialog(int id) {
        // TODO Auto-generated method stub
        if (id == 999) {
            return new DatePickerDialog(this, myDateListener, year, month, day);
        }
        if (id == 998) {
            return new TimePickerDialog(this, myTimeListenerBefore, 0, 0, true);
        }
        if (id == 997) {
            return new TimePickerDialog(this, myTimeListenerAfter, 0, 0, true);
        }
        return null;
    }

    private DatePickerDialog.OnDateSetListener myDateListener = new DatePickerDialog.OnDateSetListener() {
        @Override
        public void onDateSet(DatePicker arg0, int arg1, int arg2, int arg3) {
            showDate(arg1, arg2+1, arg3);
        }
    };

    private TimePickerDialog.OnTimeSetListener myTimeListenerBefore = new TimePickerDialog.OnTimeSetListener() {
        @Override
        public void onTimeSet (TimePicker arg0, int arg1, int arg2) {
            showTimeBefore(arg1, arg2);
        }
    };

    private TimePickerDialog.OnTimeSetListener myTimeListenerAfter = new TimePickerDialog.OnTimeSetListener() {
        @Override
        public void onTimeSet (TimePicker arg0, int arg1, int arg2) {
            showTimeAfter(arg1, arg2);
        }
    };

    private void showDate(int year, int month, int day) {
        dateView.setText(new StringBuilder().append(day).append("/")
                .append(month).append("/").append(year));
    }

    private void showTimeBefore(int hour, int minute) {
        String hourStr = Integer.toString(hour);
        if(hourStr.length() == 1){ hourStr = "0"+hourStr;}
        String minuteStr = Integer.toString(minute);
        if(minuteStr.length() == 1){ minuteStr = "0"+minuteStr;}
        before.setText(new StringBuilder().append(hourStr).append("H").append(minuteStr));
    }

    private void showTimeAfter(int hour, int minute) {
        String hourStr = Integer.toString(hour);
        if(hourStr.length() == 1){ hourStr = "0"+hourStr;}
        String minuteStr = Integer.toString(minute);
        if(minuteStr.length() == 1){ minuteStr = "0"+minuteStr;}
        after.setText(new StringBuilder().append(hourStr).append("H").append(minuteStr));
        before.setText(new StringBuilder().append(hourStr).append("H").append(minuteStr));
    }

    public void setHourEnd (View view) {
        showDialog(998);
    }

    public void setHourStart (View view) {
        showDialog(997);
    }
}
