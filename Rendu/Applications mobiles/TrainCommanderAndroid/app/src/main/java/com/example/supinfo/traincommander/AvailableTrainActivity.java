package com.example.supinfo.traincommander;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.Toast;

import com.google.android.gms.appindexing.Action;
import com.google.android.gms.appindexing.AppIndex;
import com.google.android.gms.common.api.GoogleApiClient;
import com.paypal.android.sdk.payments.PayPalConfiguration;
import com.paypal.android.sdk.payments.PayPalPayment;
import com.paypal.android.sdk.payments.PayPalService;
import com.paypal.android.sdk.payments.PaymentActivity;
import com.paypal.android.sdk.payments.PaymentConfirmation;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.math.BigDecimal;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.TimeZone;
import java.util.concurrent.ExecutionException;

public class AvailableTrainActivity extends Activity {

    private ListView listView;

    ArrayList<Journey> journeys;

    int bougthItemId;

    private static PayPalConfiguration config = new PayPalConfiguration()

            // Start with mock environment.  When ready, switch to sandbox (ENVIRONMENT_SANDBOX)
            // or live (ENVIRONMENT_PRODUCTION)
            .environment(PayPalConfiguration.ENVIRONMENT_NO_NETWORK)

            .clientId("AFcWxV21C7fd0v3bYYYRCpSSRl31AOiiVLQTxDIiUgEUWjjKLwtCqSVF");



    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);

        setContentView(R.layout.activity_available_train);

        listView = (ListView) findViewById(R.id.listView);

        journeys = new ArrayList<>();

        // load json
        Intent intent2 = getIntent();
        String url = intent2.getExtras().getString("url");
        RetrieveJson retrieveJson = new RetrieveJson();

        try {
            ArrayList<String> listStations = new ArrayList<>();
            JSONArray ja = retrieveJson.execute(url).get();
            if (ja != null){
                for (int j = 0; j < ja.length(); j++)
                {
                    JSONObject jo = ja.getJSONObject(j);
                    Journey journey = new Journey();

                    JSONArray stationsJA = jo.getJSONArray("stations");
                    journey.stations = new ArrayList<>();
                    for (int k = 0; k < stationsJA.length(); k++){
                        journey.stations.add(stationsJA.getString(k));
                    }

                    JSONArray startTimesJA = jo.getJSONArray("startTimes");
                    journey.startTimes = new ArrayList<>();
                    for (int k = 0; k < startTimesJA.length(); k++){
                        journey.startTimes.add(new Date(startTimesJA.getInt(k)*1000L));
                    }

                    JSONArray arrivalTimesJA = jo.getJSONArray("arrivalTimes");
                    journey.arrivalTimes = new ArrayList<>();
                    for (int k = 0; k < arrivalTimesJA.length(); k++){
                        journey.arrivalTimes.add(new Date(arrivalTimesJA.getInt(k)*1000L));
                    }

                    journey.price = jo.getDouble("price");
                    journeys.add(journey);
                }
            }
        } catch (InterruptedException e) {
            e.printStackTrace();
        } catch (ExecutionException e) {
            e.printStackTrace();
        } catch (JSONException e) {
            e.printStackTrace();
        }


        // for test
        final ArrayList<String> list = new ArrayList<String>();
        SimpleDateFormat sdf = new SimpleDateFormat("HH:mm");
        sdf.setTimeZone(TimeZone.getTimeZone("GMT+1"));
        for (Journey journey : journeys){
            String s = new String();
            for (int i = 0; i < journey.stations.size()-1; i+=2){
                s += journey.stations.get(i);
                s += " - ";
                s += journey.stations.get(i+1);
                s += "              ";
                int j = 0;
                if (i > 0){ j = i / 2; }
                s += sdf.format(journey.startTimes.get(j));
                s += " - ";
                s += sdf.format(journey.arrivalTimes.get(j));
                s += "\n";

            }
            long duration = journey.arrivalTimes.get(journey.arrivalTimes.size()-1).getTime()-journey.startTimes.get(0).getTime();
            duration = duration/1000;
            //duration = duration/60;
            long hours = duration/3600;
            duration = duration%3600;
            long minutes = duration/60;
            s += "Duration : "+hours+":"+minutes+"\n";
            s += "Price : ";
            s += journey.price;
            list.add(s);
        }

        /*String[] values = new String[]{"Lille flandre 14:02 32 min\n13E", "Lille flandre 14:26 38 min\n14E"};

        for (int i = 0; i < values.length; ++i) {
            list.add(values[i]);
        }*/
        final ArrayAdapter adapter = new ArrayAdapter(this,
                android.R.layout.simple_list_item_1, list);
        listView.setAdapter(adapter);


        listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view,
                                    int position, long id) {
                onBuyPressed((int)id);

            }
        });

        Intent intent = new Intent(this, PayPalService.class);
        intent.putExtra(PayPalService.EXTRA_PAYPAL_CONFIGURATION, config);

        startService(intent);
    }

    @Override
    public void onDestroy() {
        stopService(new Intent(this, PayPalService.class));
        super.onDestroy();
    }

    public void onBuyPressed(int id) {

        Journey j = journeys.get(id);
        bougthItemId = id;

        PayPalPayment payment = new PayPalPayment(new BigDecimal(j.price), "EUR", j.stations.get(0) + " - " + j.stations.get(j.stations.size() -1),
                PayPalPayment.PAYMENT_INTENT_SALE);

        Intent intent = new Intent(this, PaymentActivity.class);

        // send the same configuration for restart resiliency
        intent.putExtra(PayPalService.EXTRA_PAYPAL_CONFIGURATION, config);

        intent.putExtra(PaymentActivity.EXTRA_PAYMENT, payment);

        startActivityForResult(intent, 0);
    }

    @Override
    protected void onActivityResult (int requestCode, int resultCode, Intent data) {
        if (resultCode == Activity.RESULT_OK) {
            PaymentConfirmation confirm = data.getParcelableExtra(PaymentActivity.EXTRA_RESULT_CONFIRMATION);
            if (confirm != null) {
                try {
                    Log.i("paymentExample", confirm.toJSONObject().toString(4));

                    SharedPreferences sharedPref = getSharedPreferences("pref", Context.MODE_PRIVATE);
                    String id = sharedPref.getString("logID", "");

                    Journey j = journeys.get(bougthItemId);
                    String url = "http://api.train-commander.fr/create/history/"+j.price+"/"+j.stations.get(0)+"/"+j.stations.get(j.stations.size()-1)+"/"+j.startTimes.get(0).getTime()/1000+"/"+j.arrivalTimes.get(0).getTime()/1000+"/"+id;
                    url = url.replace(" ","%20");
                    Log.i("url", url);
                    RetrieveJson retrieveJson = new RetrieveJson();
                    retrieveJson.execute(url).get();

                    Intent i = new Intent(AvailableTrainActivity.this, HomeActivity.class);
                    startActivity(i);

                } catch (JSONException e) {
                    Log.e("paymentExample", "an extremely unlikely failure occurred: ", e);
                } catch (InterruptedException e) {
                    e.printStackTrace();
                } catch (ExecutionException e) {
                    e.printStackTrace();
                }
            }
        }
        else if (resultCode == Activity.RESULT_CANCELED) {
            Log.i("paymentExample", "The user canceled.");
        }
        else if (resultCode == PaymentActivity.RESULT_EXTRAS_INVALID) {
            Log.i("paymentExample", "An invalid Payment or PayPalConfiguration was submitted. Please see the docs.");
        }
    }
}
