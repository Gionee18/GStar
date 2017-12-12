package com.gionee.gioneeabc.helpers;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.os.AsyncTask;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.TextView;

import com.gionee.gioneeabc.R;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;


/**
 * Created by LinchPin on 6/16/2015.
 */
public class NetworkTaskNew extends AsyncTask<String, String, String> {
    private boolean isDoinBackground, isPostExecute, isPreExecute, isProgressDialog = true;
    private String dialogMessage = "Loading";
    private DoInBackground doInBackground;
    private Result result;
    private PreNetwork preNetwork;
    private ProgressDialog pd;
    private Context ctx;
    private int id;
    private int arg1;
    private String arg2;
    private boolean isDialogAndTaskCancellable = false;


    public NetworkTaskNew(Context ctx) {
        this.ctx = ctx;
    }

    public NetworkTaskNew(Context ctx, int id) {
        this.ctx = ctx;
        this.id = id;
    }

    public NetworkTaskNew(Context ctx, String arg2) {
        this.ctx = ctx;
        this.arg2 = arg2;
    }

    public NetworkTaskNew(Context ctx, int id, int arg1, String arg2) {
        this.ctx = ctx;
        this.id = id;
        this.arg1 = arg1;
        this.arg2 = arg2;
    }

    public void setDialogMessage(String dialogMessage) {
        this.dialogMessage = dialogMessage;
    }

    public void exposeDoInBackground(DoInBackground doInBackground) {
        this.isDoinBackground = true;
        this.doInBackground = doInBackground;
    }

    public void exposePostExecute(Result result) {
        this.isPostExecute = true;
        this.result = result;
    }

    public void exposePreExecute(PreNetwork preNetwork) {
        this.isPreExecute = true;
        this.preNetwork = preNetwork;
    }

    public void setProgressDialog(boolean isProgress) {
        isProgressDialog = isProgress;
    }


    public interface DoInBackground {

        String doInBackground(Integer id, String... params);
    }

    public interface Result {
        void resultFromNetwork(String object, int id, int arg1, String arg2) throws JSONException;
    }

    public interface PreNetwork {

        void preNetwork(int id);
    }

    @Override
    protected void onPreExecute() {

        if (ctx != null) {
            if (isProgressDialog) {
                pd = new ProgressDialog(ctx, R.style.TransparentProgressDialog);
                pd.setOnCancelListener(new DialogInterface.OnCancelListener() {
                    @Override
                    public void onCancel(DialogInterface dialog) {
                        NetworkTaskNew.this.cancel(true);
                    }
                });
                pd.setMessage(dialogMessage);
                pd.setTitle(null);
                pd.show();

                pd.setProgressStyle(R.style.TransparentProgressDialog);
                LayoutInflater inflater = LayoutInflater.from(ctx);
                View v = inflater.inflate(R.layout.custom_progress_dialog, null);
                ((TextView) v.findViewById(R.id.lodingText)).setText(dialogMessage);
                pd.setContentView(v);
                pd.setCancelable(isDialogAndTaskCancellable);
                pd.setIndeterminate(true);
            }
            if (isPreExecute && preNetwork != null)
                preNetwork.preNetwork(id);

        }
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String... params) {
        Log.d("Hem", "inside doInbackground" + isDoinBackground);
        String responseString = null;
        if (isDoinBackground && doInBackground != null) {
            return doInBackground.doInBackground(id, params);

        } else if (params.length == 1) {
            responseString = httpGetRaw(params[0]);

        } else {
            String url = params[0];
            String requestJSONString = params[1];
            responseString = httpPostRaw(url, requestJSONString);
        }
        return responseString;
    }

    @Override
    protected void onPostExecute(String myresult) {
        try {
            if (ctx != null) {
                if (pd != null && isProgressDialog && pd.isShowing()) pd.dismiss();

                if (isPostExecute && result != null) {
                    if (myresult.contains("access_denied")) {
                        try {
                            JSONObject jsonObject = new JSONObject(myresult);
                            if (jsonObject.has("error") && jsonObject.getString("error").equalsIgnoreCase("access_denied")) {
                                Util.logoutwithMessage((Activity) ctx);
                            } else
                                result.resultFromNetwork(myresult, id, arg1, arg2);
                        } catch (Exception ex) {
                            result.resultFromNetwork(myresult, id, arg1, arg2);
                        }
                    } else {
                        result.resultFromNetwork(myresult, id, arg1, arg2);
                    }
                }
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
        super.onPostExecute(myresult);
    }

    public void setDialogAndTaskCancellable(boolean isDialogAndTaskCancellable) {
        this.isDialogAndTaskCancellable = isDialogAndTaskCancellable;
    }

    public static String httpGetRaw(String url) {
        String response = "";
        BufferedReader br = null;
        try {
            URL urlObj = new URL(url);
            HttpURLConnection conn = (HttpURLConnection) urlObj.openConnection();
            conn.setRequestMethod("GET");
/*            conn.setDoOutput(true);
            conn.setUseCaches(false);
            conn.setRequestProperty("Content-Type", "application/json");*/

            System.out.println("Response Code: " + conn.getResponseCode());

            br = new BufferedReader(new InputStreamReader(conn.getInputStream()));
            StringBuilder sb = new StringBuilder();
            String line;


            while ((line = br.readLine()) != null)
                sb.append(line);
            response = sb.toString();

        } catch (Exception e) {
            e.printStackTrace();

        } finally {
            if (br != null) {
                try {
                    br.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            }
        }
        return response;
    }

    public static String httpPostRaw(String url, String jsonData) {
        String response = "";
        BufferedReader br = null;
        try {
            URL urlObj = new URL(url);
            HttpURLConnection conn = (HttpURLConnection) urlObj.openConnection();
            conn.setRequestMethod("POST");
            conn.setDoOutput(true);
            conn.setUseCaches(false);
            conn.setRequestProperty("Content-Type", "application/json");

            // For POST only - BEGIN
            if (jsonData != null && !jsonData.equals("")) {
                OutputStreamWriter out = new OutputStreamWriter(conn.getOutputStream());
                out.write(jsonData);
                out.close();
            }

            // For POST only - END
            // read the response

            System.out.println("Response Code: " + conn.getResponseCode());
            InputStream in = new BufferedInputStream(conn.getInputStream());
            StringBuilder sb = new StringBuilder();

            String line;

            br = new BufferedReader(new InputStreamReader(in));
            while ((line = br.readLine()) != null)
                sb.append(line);
            response = sb.toString();
            Log.d("URL:", url);
            Log.d("Request:", jsonData);
            Log.d("Response:", response);

        } catch (Exception e) {
            e.printStackTrace();

        } finally {
            if (br != null) {
                try {
                    br.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            }
        }
        return response;
    }
}