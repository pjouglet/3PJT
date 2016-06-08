package com.example.supinfo.traincommander;

import android.os.AsyncTask;
import android.util.Log;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.URL;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by Yoaime on 5/21/2016.
 */
public class Connect extends AsyncTask<String, String, JSONObject> {
    protected JSONObject doInBackground(String... args) {
        return getJSONFromUrl(args[0]);
    }


    public JSONObject getJSONFromUrl(String url) {
        InputStream is = null;
        JSONArray jObj = null;
        String json = "";

        try{
            URL url2 = new URL(url);
            HttpURLConnection connection = (HttpURLConnection)url2.openConnection();
            connection.setRequestMethod("GET");
            connection.connect();

            /*int code = connection.getResponseCode();
            if (code != 200)
            {
                return "1";
            }*/
        }
        catch (Exception e){
            e.printStackTrace();
        }
        try {
            DefaultHttpClient httpClient = new DefaultHttpClient();
            HttpGet httpGet = new HttpGet(url);

            HttpResponse httpResponse = httpClient.execute(httpGet);
            HttpEntity httpEntity = httpResponse.getEntity();
            is = httpEntity.getContent();

        } catch (UnsupportedEncodingException e) {
            e.printStackTrace();
        } catch (ClientProtocolException e) {
            e.printStackTrace();
        } catch (IOException e) {
            e.printStackTrace();
        }

        try {
            BufferedReader reader = new BufferedReader(new InputStreamReader(is));
            StringBuilder sb = new StringBuilder();
            String line = null;
            while ((line = reader.readLine()) != null) {
                sb.append(line + "\n");
            }
            is.close();
            Log.i("debug lala", sb.toString());
            return new JSONObject(sb.toString());
        } catch (Exception e) {
            Log.e("Buffer Error", "Error converting result " + e.toString());

        }

        /*try {
            jObj = new JSONArray(json);
        } catch (JSONException e) {
            Log.e("JSON Parser", "Error parsing data " + e.toString());
            try {
                jObj = new JSONArray().put(new JSONObject(json));
            } catch (JSONException e1) {
                e1.printStackTrace();
            }
        }

        return jObj;*/
        JSONObject jo = null;
        try {
            jo = new JSONObject("{\"id\":\"0\"}");
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return jo;
    }
}
