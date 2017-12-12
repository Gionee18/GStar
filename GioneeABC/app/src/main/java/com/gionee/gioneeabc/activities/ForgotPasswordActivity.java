package com.gionee.gioneeabc.activities;

import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.helpers.GStarApplication;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.Util;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;

/**
 * Created by Linchpin25 on 2/22/2016.
 */
public class ForgotPasswordActivity extends AppCompatActivity implements NetworkTask.Result, OnClickListener {
    EditText etMail;
    TextView tvSubmit;
    LinearLayout llMain;
    private final int FORGOT_PASS = 100;
    NetworkTask networkTask;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.forgot_pass_activity);

        GStarApplication.getInstance().trackScreenView("Forgot Password Screen");

        etMail = (EditText) findViewById(R.id.etMail);

        llMain = (LinearLayout) findViewById(R.id.llMain);

        tvSubmit = (TextView) findViewById(R.id.tvSubmit);
        tvSubmit.setOnClickListener(this);


    }

    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {
        if (object != null && !object.equals("")) {
            try {
                JSONObject main = new JSONObject(object);
                if (main.has("status") && main.optString("status").equals("success")) {
                    Util.createSnackBar(llMain, getString(R.string.msg_password_changed_successfully));
                    etMail.setText("");
                } else {
                    Util.createSnackBar(llMain, getString(R.string.msg_provide_registered_email));
                }


            } catch (JSONException e) {
                e.printStackTrace();
                Util.createSnackBar(llMain, getString(R.string.msg_no_response));
            }


        } else {
            Util.createSnackBar(llMain, getString(R.string.msg_no_response));
        }


    }

    @Override
    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.tvSubmit:
                Util.hideKeyBoard(this);
                if (Util.isNetworkAvailable(ForgotPasswordActivity.this)) {
                    if (checkValidation()) {
                        ArrayList<NameValuePair> params = new ArrayList<NameValuePair>();
                        params.add(new BasicNameValuePair("email", etMail.getText().toString()));
                        networkTask = new NetworkTask(ForgotPasswordActivity.this, FORGOT_PASS, params, null);
                        networkTask.exposePostExecute(ForgotPasswordActivity.this);
                        networkTask.execute(NetworkConstants.FORGOT_PASSWORD_URL);
                    }
                } else
                    Util.createSnackBar(llMain, getString(R.string.msg_no_internet));
                break;
        }

    }

    private boolean checkValidation() {
        if (etMail.getText().toString().equals("")) {
            Util.createSnackBar(llMain, getString(R.string.err_mail_msg));
            return false;
        } else if (!Util.isValidEmail(etMail.getText().toString())) {
            Util.createSnackBar(llMain, getString(R.string.err_invalid_mail_msg));
            return false;
        }

        return true;

    }


}
