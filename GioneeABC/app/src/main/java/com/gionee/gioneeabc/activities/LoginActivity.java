package com.gionee.gioneeabc.activities;

import android.content.Intent;
import android.database.Cursor;
import android.graphics.Typeface;
import android.os.Bundle;
import android.provider.Settings;
import android.support.v7.app.AppCompatActivity;
import android.text.method.PasswordTransformationMethod;
import android.view.View;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.bean.UserBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.GStarApplication;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.NetworkTask.Result;
import com.gionee.gioneeabc.helpers.NetworkTaskNew;
import com.gionee.gioneeabc.helpers.Util;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by Linchpin25 on 1/29/2016.
 */
public class LoginActivity extends AppCompatActivity implements View.OnClickListener, Result, NetworkTaskNew.Result {

    private final int LOGIN = 101, GET_USER_INFO = 102;
    private static final int DEVICE_REGISTETR = 104;
    private static final int AUDIT = 103;
    EditText etMail, etPass;
    TextView tvSubmit, tvForgotPass;
    LinearLayout llMain;
    NetworkTask networkTask;
    private CheckBox saveCheckBox;
    UserBean user;
    DataBaseHandler dbHandler;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.login_activity);

        GStarApplication.getInstance().trackScreenView("Login Screen");

        if (DataStore.isLoggedIn(this, DataStore.KEY_LOGGED_IN))
            startActivity(new Intent(LoginActivity.this, MainActivity.class));


        Util.deleteFolderContent();

        etMail = (EditText) findViewById(R.id.etMail);
        etPass = (EditText) findViewById(R.id.etPass);
        etPass.setTypeface(Typeface.DEFAULT);
        etPass.setTransformationMethod(new PasswordTransformationMethod());
        tvSubmit = (TextView) findViewById(R.id.tvSubmit);
        tvSubmit.setOnClickListener(this);
        tvForgotPass = (TextView) findViewById(R.id.tvForgotPass);
        tvForgotPass.setOnClickListener(this);
        llMain = (LinearLayout) findViewById(R.id.llMain);
        dbHandler = DataBaseHandler.getInstance(LoginActivity.this);
        saveCheckBox = (CheckBox) findViewById(R.id.save_chk);

        if (DataStore.getCredentialsSave(this)) {
            etMail.setText(DataStore.getEmail(this, DataStore.USER_EMAIL));
            etMail.setSelection(etMail.getText().length());
            etPass.setText(DataStore.getPass(this, DataStore.USER_PASS));
            etPass.setSelection(etPass.getText().length());
            saveCheckBox.setChecked(true);
        }

    }


    @Override
    public void onClick(View v) {

        switch (v.getId()) {
            case R.id.tvSubmit:
                Util.hideKeyBoard(this);
                if (Util.isNetworkAvailable(LoginActivity.this)) {
                    if (checkValidation()) {

                        DataStore.setEmail(this, etMail.getText().toString().trim());
                        DataStore.setPass(this, etPass.getText().toString().trim());
                        DataStore.setCredentialsSave(this, saveCheckBox.isChecked());
                        List<NameValuePair> params = new ArrayList<NameValuePair>();
                        params.add(new BasicNameValuePair("grant_type", "password"));
                        params.add(new BasicNameValuePair("client_id", "f3d259ddd3ed8ff3843839b"));
                        params.add(new BasicNameValuePair("client_secret", "4c7f6f8fa93d59c45502c0ae8c4a95b"));
                        params.add(new BasicNameValuePair("username", etMail.getText().toString().trim()));
                        params.add(new BasicNameValuePair("password", etPass.getText().toString().trim()));
                        networkTask = new NetworkTask(LoginActivity.this, LOGIN, params);
                        networkTask.exposePostExecute(LoginActivity.this);
                        networkTask.execute(NetworkConstants.LOGIN_URL);

                        DataBaseHandler.getInstance(this).deleteSubmitLogout();
                    }
                } else {
                    Util.createSnackBar(llMain, getString(R.string.msg_no_internet));
                }

                break;
            case R.id.tvForgotPass:
                Util.hideKeyBoard(this);
                startActivity(new Intent(LoginActivity.this, ForgotPasswordActivity.class));
                break;
        }


    }

    private boolean checkValidation() {
        if (etMail.getText().toString().trim().equals("")) {
            Util.createSnackBar(llMain, getString(R.string.err_mail_msg));
            //Util.createSnackBar(llMain, getString(R.string.err_mail_msg));
            return false;

        } else if (!Util.isValidEmail(etMail.getText().toString().trim())) {
            Util.createSnackBar(llMain, getString(R.string.err_invalid_mail_msg));
            // Util.createSnackBar(llMain, getString(R.string.err_invalid_mail_msg));
            return false;
        } else if (etPass.getText().toString().trim().equals("")) {
            Util.createSnackBar(llMain, getString(R.string.err_pass_msg));
            // Util.createSnackBar(llMain, getString(R.string.err_pass_msg));
            return false;
        }
        return true;

    }


    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {
        if (object != null && !object.equals("")) {
            try {
                JSONObject main = new JSONObject(object);
                if (id == LOGIN) {
                    if (main.has("error")) {
                        etPass.setText("");
                        Util.createSnackBar(llMain, main.optString("error_description"));
                    } else {
                        DataStore.setAuthToken(LoginActivity.this, main.optString("access_token"));
                        networkTask = new NetworkTask(LoginActivity.this, GET_USER_INFO, null, null, "Getting User Info");
                        networkTask.exposePostExecute(LoginActivity.this);
                        networkTask.execute(NetworkConstants.USER_INFO_URL + DataStore.getAuthToken(LoginActivity.this, DataStore.AUTH_TOKEN));
                    }
                } else if (id == GET_USER_INFO) {
                    if (main.has("status") && main.getString("status").equalsIgnoreCase("success")) {
                        JSONObject userDetail = main.getJSONObject("data");
                        DataStore.setDisclaimer(this, main.optString("disclaimer_text"));
//                        if (userDetail.getString("role").equals("30")) {

                        user = new UserBean();
                        user.setUserId(userDetail.optInt("id"));
                        user.setUserName(userDetail.optString("first_name") + " " + userDetail.optString("last_name"));
                        user.setUserEmail(userDetail.optString("email"));
                        user.setStatus(userDetail.optString("status"));
                        user.setUserImage(userDetail.optString("profile_picture"));
                        user.setUserImageServerUrl(main.optString("userImagePath"));
                        user.setUserImageLocalUrl("");
                        DataStore.setlastLogin(this, userDetail.optString("last_login"));

                        DataStore.setLoggedIn(this, true);
                        setNotificationKeyonServer();
                        updateAuditTrailDataOnServer();

                        Intent i = new Intent(LoginActivity.this, MainActivity.class);
                        i.putExtra("user", user);
                        startActivity(i);
                        finish();
                    } else {
                        Util.createSnackBar(llMain, getString(R.string.msg_no_response));
                    }
                } else if (id == DEVICE_REGISTETR) {
                    if (main.has("status") && main.getString("status").equalsIgnoreCase("success")) {
                        DataStore.setIsFCMTokenUpdate(this, true);
                    }
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }

        }
    }

    private void updateAuditTrailDataOnServer() {
        if (!auditJsonData().equalsIgnoreCase("")) {
            NetworkTaskNew networkTask = new NetworkTaskNew(this, AUDIT);
            networkTask.exposePostExecute(this);
            networkTask.execute(NetworkConstants.AUDIT_TRAIL, auditJsonData());
        }
    }

    private String auditJsonData() {
        try {
            boolean isDataAvilable = false;
            JSONObject Obj = new JSONObject();
            JSONArray jsonArray = new JSONArray();
            JSONObject jsonObject;

            Cursor cursor = DataBaseHandler.getInstance(this).getAllAuditTrailData(user.getUserId());
            if (cursor.moveToFirst()) {
                do {
                    String moduleName = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_MODULE_NAME));
                    String time = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_ACCESS_TIME));
                    String login_time = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_LAST_LOGIN));
                    jsonObject = new JSONObject();
                    jsonObject.put("module_name", moduleName);
                    jsonObject.put("access_time", time);
                    jsonObject.put("login_time", login_time);
                    jsonArray.put(jsonObject);
                    isDataAvilable = true;
                } while (cursor.moveToNext());
            }
            if (isDataAvilable) {
                Obj.put("data", jsonArray);
                Obj.put("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN));
                Obj.put("user_id", user.getUserId());

                String temp = Obj.toString();
                return temp;
            } else return "";
        } catch (Exception ex) {
            return "";
        }
    }

    private void setNotificationKeyonServer() {

        boolean isFCMTokenUpdate = DataStore.getIsFCMTokenUpdate(this);

        if (isFCMTokenUpdate == false) {
            String android_id = Settings.Secure.getString(this.getContentResolver(), Settings.Secure.ANDROID_ID);
            String FCMToken = DataStore.getFCMToken(this);
            String accessToken = DataStore.getAuthToken(this, DataStore.AUTH_TOKEN);
            if (Util.isNetworkAvailable(this)) {
                List<NameValuePair> params = new ArrayList<NameValuePair>();
                params.add(new BasicNameValuePair("device_id", android_id));
                params.add(new BasicNameValuePair("device_token", FCMToken));
                params.add(new BasicNameValuePair("access_token", accessToken));


                NetworkTask networkTask = new NetworkTask(this, DEVICE_REGISTETR, params);
                networkTask.setProgressDialog(false);
                networkTask.exposePostExecute(this);
                networkTask.execute(NetworkConstants.DEVICE_REGISTETR);

            }
        }
    }


    @Override
    public void resultFromNetwork(String object, int id, int arg1, String arg2) {
        try {
            JSONObject jsonObject = new JSONObject(object);

            if ((jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {

                DataBaseHandler.getInstance(this).deleteAllAuditTrailData(user.getUserId());
            }
        } catch (Exception ex1) {

        }
    }
}
