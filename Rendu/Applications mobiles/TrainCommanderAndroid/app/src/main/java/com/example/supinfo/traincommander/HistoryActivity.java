package com.example.supinfo.traincommander;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.pdf.PdfDocument;
import android.net.Uri;
import android.os.Environment;
import android.print.PrintAttributes;
import android.print.pdf.PrintedPdfDocument;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
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

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.math.BigDecimal;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.TimeZone;
import java.util.concurrent.ExecutionException;

public class HistoryActivity extends Activity {

    private ListView listView;

    ArrayList<JourneyHistory> journeysHistory;



    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);

        setContentView(R.layout.activity_history);

        listView = (ListView) findViewById(R.id.listView);

        journeysHistory = new ArrayList<>();

        // load json
        Intent intent2 = getIntent();
        String url = intent2.getExtras().getString("url");
        RetrieveJson retrieveJson = new RetrieveJson();

        try {
            JSONArray ja = retrieveJson.execute(url).get();
            if (ja != null){
                for (int j = 0; j < ja.length(); j++)
                {
                    JSONObject jo = ja.getJSONObject(j);
                    JourneyHistory journey = new JourneyHistory();

                    journey.departure = jo.getString("startStation");
                    journey.arrival = jo.getString("arrivalStation");
                    journey.startTimes = jo.getString("startTime");
                    journey.arrivalTimes = jo.getString("arrivalTime");
                    journey.commandTime = jo.getString("commandTime");
                    journey.price = jo.getDouble("price");
                    journeysHistory.add(journey);
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
        for (JourneyHistory journey : journeysHistory){
            String s = new String();
            s += journey.departure;
            s += "          ";
            s += journey.startTimes;
            s += "\n";
            s += journey.arrival;
            s += "          ";
            s += journey.arrivalTimes;
            s += "\n";
            s += "Price : ";
            s += journey.price;
            s += "\n";
            s += "Order date : ";
            s += journey.commandTime;
            list.add(s);
        }

        final ArrayAdapter adapter = new ArrayAdapter(this,
                android.R.layout.simple_list_item_1, list);
        listView.setAdapter(adapter);
        listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(final AdapterView<?> parent, final View view,
                                    int position, long id) {
                final int pos = position;
                AlertDialog.Builder builder = new AlertDialog.Builder(parent.getContext());
                builder.setMessage("What do you want to do with this journey ?");
                builder.setPositiveButton("Reorder", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        // User clicked OK button
                        JourneyHistory j = journeysHistory.get(pos);
                        Intent i = new Intent(HistoryActivity.this, SearchTrainActivity.class);
                        i.putExtra("startStation", j.departure);
                        i.putExtra("arrivalStation", j.arrival);
                        i.putExtra("startTime", j.startTimes);
                        startActivity(i);
                    }
                });
                builder.setNegativeButton("Print", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        // User cancelled the dialog
                        PdfDocument document = new PdfDocument();
                        PdfDocument.PageInfo pageInfo = new PdfDocument.PageInfo.Builder(1000, 1000, 1).create();
                        PdfDocument.Page page = document.startPage(pageInfo);
                        String str = listView.getItemAtPosition(pos).toString();
                        Log.i("debug", str);
                        Connect retrieveJson = new Connect();
                        SharedPreferences sharedPref = getSharedPreferences("pref", Context.MODE_PRIVATE);
                        String idUser = sharedPref.getString("logID", "");
                        String url = "http://api.train-commander.fr/user/"+idUser;
                        try {
                            JSONObject jo = retrieveJson.execute(url).get();
                            String firstname = jo.getString("firstname");
                            String lastname = jo.getString("lastname");
                            String str2 = firstname + " " + lastname + "\n" + str;
                            Log.i("str2", str);
                            LinearLayout layout = new LinearLayout(parent.getContext());
                            layout.setOrientation(LinearLayout.VERTICAL);
                            TextView content = new TextView(parent.getContext());
                            content.setVisibility(View.VISIBLE);
                            content.setText(str2);
                            layout.addView(content);
                            LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(
                                    ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT);

                            params.gravity = Gravity.BOTTOM;

                            ImageView qrCode = new ImageView(parent.getContext());
                            qrCode.setImageResource(R.drawable.qr_code);
                            qrCode.setVisibility(View.VISIBLE);

                            layout.addView(qrCode, params);
                            layout.measure(1000, 1000);
                            layout.layout(0, 0, 1000, 1000);
                            //View v = content;
                            layout.draw(page.getCanvas());
                            document.finishPage(page);
                            File mypath=new File( Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOWNLOADS),"myTicket.pdf");
                            try {
                                document.writeTo(new FileOutputStream(mypath));
                            } catch (IOException e) {
                                e.printStackTrace();
                            }

                            document.close();
                        } catch (InterruptedException e) {
                            e.printStackTrace();
                        } catch (ExecutionException e) {
                            e.printStackTrace();
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }

                    }
                });
                AlertDialog dialog = builder.create();
                dialog.show();
            }
        });
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
    }
}
