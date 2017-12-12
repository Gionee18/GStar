package com.gionee.gioneeabc.activities;

import android.content.DialogInterface;
import android.graphics.Typeface;
import android.os.Bundle;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.text.method.PasswordTransformationMethod;
import android.view.View;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.bean.UserBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.Util;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by Linchpin25 on 1/28/2016.
 */
public class ChangePasswordActivity extends AppCompatActivity implements View.OnClickListener, NetworkTask.Result {

    EditText etOldPass, etNewPass, etConfirmPass;
    TextView tvSubmit;
    NetworkTask networkTask;
    LinearLayout llMain;
    private final int CHANGE_PASS = 101;
    UserBean user;
    DataBaseHandler dbHandler;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.change_pass_activity);

        llMain = (LinearLayout) findViewById(R.id.llMain);

        etOldPass = (EditText) findViewById(R.id.etOldPass);
        etOldPass.setTypeface(Typeface.DEFAULT);
        etOldPass.setTransformationMethod(new PasswordTransformationMethod());

        etNewPass = (EditText) findViewById(R.id.etNewPass);
        etNewPass.setTypeface(Typeface.DEFAULT);
        etNewPass.setTransformationMethod(new PasswordTransformationMethod());

        etConfirmPass = (EditText) findViewById(R.id.etConfirmPass);
        etConfirmPass.setTypeface(Typeface.DEFAULT);
        etConfirmPass.setTransformationMethod(new PasswordTransformationMethod());

        tvSubmit = (TextView) findViewById(R.id.tvSubmit);
        tvSubmit.setOnClickListener(this);

        dbHandler = DataBaseHandler.getInstance(this);
        user = dbHandler.getUser();


    }

    @Override
    public void onClick(View v) {

        switch (v.getId()) {
            case R.id.tvSubmit:
                if (checkValidation()) {
                    if (Util.isNetworkAvailable(ChangePasswordActivity.this)) {
                        List<NameValuePair> params = new ArrayList<NameValuePair>();
                        params.add(new BasicNameValuePair("oldpassword", etOldPass.getText().toString()));
                        params.add(new BasicNameValuePair("newpassword", etNewPass.getText().toString()));
                        params.add(new BasicNameValuePair("confirmpassword", etConfirmPass.getText().toString()));
                        params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN)));
                        params.add(new BasicNameValuePair("id", "" + user.getUserId()));


                        networkTask = new NetworkTask(ChangePasswordActivity.this, CHANGE_PASS, params, null);
                        networkTask.exposePostExecute(ChangePasswordActivity.this);
                        networkTask.execute(NetworkConstants.CHANGE_PASSWORD_URL);

                    } else {
                        Util.createSnackBar(llMain, getString(R.string.msg_no_internet));
                    }

                }

                break;
        }


    }

    private boolean checkValidation() {
        if (etOldPass.getText().toString().equals("")) {
            Util.createSnackBar(llMain, getString(R.string.err_old_pass_msg));
            return false;

        } else if (etNewPass.getText().toString().equals("")) {
            Util.createSnackBar(llMain, getString(R.string.err_new_pass_msg));
            return false;
        } else if (etConfirmPass.getText().toString().equals("")) {
            Util.createSnackBar(llMain, getString(R.string.err_confirm_pass_msg));
            return false;
        } else if (!etConfirmPass.getText().toString().equals(etNewPass.getText().toString())) {
            Util.createSnackBar(llMain, getString(R.string.err_new_confirm_pass_not_match_msg));
            return false;
        }
        return true;

    }

    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {

        if (object != null && !object.equals("")) {
            try {
                JSONObject main = new JSONObject(object);
                if (id == CHANGE_PASS) {

                    if (main.optString("status").equals("success")) {
                        JSONObject result = main.getJSONObject("result");
//                        Util.createSnackBar(llMain, result.optString("msg"));
                        etOldPass.setText("");
                        etNewPass.setText("");
                        etConfirmPass.setText("");
                        AlertDialog.Builder builder = new AlertDialog.Builder(this);

                        builder.setMessage(result.optString("msg"))
                                .setCancelable(false)
                                .setPositiveButton("OK", new DialogInterface.OnClickListener() {
                                    public void onClick(DialogInterface dialog, int id) {
                                        dialog.dismiss();
                                        finish();
                                    }
                                });
                        AlertDialog alert = builder.create();
                        alert.show();

                    } else {
                        Util.createSnackBar(llMain, getString(R.string.msg_no_response));
                    }


                }


            } catch (JSONException e) {
                e.printStackTrace();
            }
        } else {
            Util.createSnackBar(llMain, getString(R.string.msg_no_response));
        }
    }
}
