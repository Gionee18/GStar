package com.gionee.gioneeabc.helpers;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;
import android.view.View;
import android.view.WindowManager;
import android.widget.TextView;

import com.gionee.gioneeabc.R;

import org.apache.http.NameValuePair;
import org.json.JSONObject;

import java.io.File;
import java.util.List;

/**
 * Created by LinchPin on 6/16/2015.
 */
public class NetworkTask extends AsyncTask<String, String, String> {
    private boolean isDoinBackground, isPostExecute, isPreExecute, isProgressDialog = true;
    private String dialogMessage = "Please wait..";
    private DoinBackgroung doinBackgroung;
    private Result result;
    private PreNetwork preNetwork;
    private ProgressDialog pd;
    private Context ctx;
    private int id;
    private Object arg1, arg2;
    private List<NameValuePair> nameValuePairs, headerList;
    private String jsonParams;
    private boolean imageUpload = false;
    File fileParts;

    public NetworkTask(Context ctx, int id, String jsonParam) {
        this.ctx = ctx;
        this.id = id;
        this.jsonParams = jsonParam;
    }

    public NetworkTask(Context ctx, int id) {
        this.ctx = ctx;
        this.id = id;

    }

    public NetworkTask(Context ctx, int id, List<NameValuePair> nameValuePairs, List<NameValuePair> headerList, String dialogMessage) {
        this.ctx = ctx;
        this.id = id;
        this.headerList = headerList;
        this.nameValuePairs = nameValuePairs;
        this.dialogMessage = dialogMessage;
    }

    public NetworkTask(Context ctx, int id, List<NameValuePair> nameValuePairs, List<NameValuePair> headerList) {
        this.ctx = ctx;
        this.id = id;
        this.headerList = headerList;
        this.nameValuePairs = nameValuePairs;

    }

    public NetworkTask(Context ctx, int id, boolean imageUpload, List<NameValuePair> nameValuePairs, File fileParts) {
        this.ctx = ctx;
        this.imageUpload = imageUpload;
        this.fileParts = fileParts;
        this.id = id;
        this.nameValuePairs = nameValuePairs;

    }

    public NetworkTask(Context ctx, int id, List<NameValuePair> nameValuePairs) {
        this.ctx = ctx;
        this.id = id;
        this.nameValuePairs = nameValuePairs;
    }

    public NetworkTask(Context ctx, int id, Object arg1, Object arg2) {
        this.ctx = ctx;
        this.id = id;
        this.arg1 = arg1;
        this.arg2 = arg2;
    }

    public void exposeDoinBackground(DoinBackgroung doinBackgroung) {
        this.isDoinBackground = true;
        this.doinBackgroung = doinBackgroung;
    }

    public void exposePostExecute(Result result) {
        this.isPostExecute = true;
        this.result = result;
    }

    public void exposePreExecute(PreNetwork preNetwork) {
        this.isPreExecute = true;
        this.preNetwork = preNetwork;
    }

    public interface DoinBackgroung {

        String doInBackground(Integer id, String... params);
    }

    public interface Result {
        void resultFromNetwork(String object, int id, Object arg1, Object arg2);
    }

    public interface PreNetwork {
        void preNetwork(int id);
    }

    @Override
    protected void onPreExecute() {
        if (isProgressDialog) {
            pd = showProgressDialog(ctx);
            WindowManager.LayoutParams lp = pd.getWindow().getAttributes();
            lp.dimAmount = 0.8f;
            pd.getWindow().setAttributes(lp);
            pd.getWindow().addFlags(WindowManager.LayoutParams.FLAG_BLUR_BEHIND);

        }
        if (isPreExecute && preNetwork != null) preNetwork.preNetwork(id);

        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String... params) {

        String responseString = null;
        if (isDoinBackground && doinBackgroung != null) {

            return doinBackgroung.doInBackground(id, params);
        } else {
            String url = params[0];
            if (imageUpload)
                responseString = Util.sendRequestImageToServer(url, nameValuePairs, this.fileParts);
            else if (this.nameValuePairs == null)
                responseString = Util.httpGetRaw(url, headerList);
            else
                responseString = Util.httpPostRaw(url, this.nameValuePairs, headerList);

            System.out.println("response::" + responseString);
        }
        return responseString;
    }

    @Override
    protected void onPostExecute(String myresult) {
        if (isProgressDialog && pd.isShowing()) pd.dismiss();
        if (isPostExecute && result != null) {
            Log.e("response", myresult);
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
        super.onPostExecute(myresult);
    }

    public ProgressDialog showProgressDialog(Context context) {
        ProgressDialog pd = new ProgressDialog(context, R.style.TransparentProgressDialog);
        pd.setMessage(dialogMessage);
        pd.setTitle(null);
        pd.show();

        pd.setProgressStyle(R.style.TransparentProgressDialog);
        View v = View.inflate(context, R.layout.custom_progress_dialog, null);
        ((TextView) v.findViewById(R.id.lodingText)).setText(dialogMessage);
        pd.setContentView(v);
        pd.setCancelable(false);
        pd.setIndeterminate(true);
        return pd;
    }

    public void setProgressDialog(boolean isProgressDialog) {
        this.isProgressDialog = isProgressDialog;
    }

    public void hideProgressDialog() {
        if (isProgressDialog && pd.isShowing())
            pd.dismiss();
    }
}
