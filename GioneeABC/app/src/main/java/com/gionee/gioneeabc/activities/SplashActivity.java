package com.gionee.gioneeabc.activities;

import android.app.Activity;
import android.app.Dialog;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.gionee.gioneeabc.BuildConfig;
import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.RuntimePermissionsManager;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by Linchpin25 on 3/10/2016.
 */
public class SplashActivity extends Activity implements NetworkTask.Result {
    private static final int CHECK = 101;

    NetworkTask networkTask;

    private final int SPLASH_TIME = 3000;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
//        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
//                WindowManager.LayoutParams.FLAG_FULLSCREEN);
        setContentView(R.layout.splash_screen);

        new Handler().postDelayed(new Runnable() {
            @Override
            public void run() {
                if (RuntimePermissionsManager.isPermissionCheckOpen()) {
                    if (RuntimePermissionsManager.isBuildSysNeedRequiredPermissions()) {

                        if (RuntimePermissionsManager.hasNeedRequiredPermissions(SplashActivity.this)) {

                            Log.i("GQuestion", "need required permission.");

                            RuntimePermissionsManager
                                    .requestRequiredPermissions(
                                            SplashActivity.this,RuntimePermissionsManager.REQUIRED_PERMISSIONS_REQUEST_CODE);
                        } else
                            checkVersionOnServer();
                    } else
                        checkVersionOnServer();

                }

            }
        }, 3000);
    }

    private void enterInApp() {
        if (DataStore.isLoggedIn(SplashActivity.this, DataStore.KEY_LOGGED_IN)) {
            Intent intent = new Intent(SplashActivity.this, MainActivity.class);
            startActivity(intent);
            SplashActivity.this.finish();
        } else {
            Intent intent = new Intent(SplashActivity.this, LoginActivity.class);
            startActivity(intent);
            SplashActivity.this.finish();
        }
    }



    @Override
    public void onRequestPermissionsResult(int requestCode, String[] permissions, int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        if (!RuntimePermissionsManager.isRequestPermissionsCode(requestCode)) {
            return;
        }
        checkVersionOnServer();
//        if (RuntimePermissionsManager.hasDeniedPermissions(permissions,
//                grantResults)) {
//            Log.e("GStar", "onRequestPermissionsResult deny");
//            finish();
//
//
//        } else {
////            Intent intent = new Intent(SplashActivity.this, MainActivity.class);
////            startActivity(intent);
////            finish();
//            checkVersionOnServer();
//
//        }
    }

    private void checkVersionOnServer() {
        networkTask = new NetworkTask(this, CHECK);
        networkTask.exposePostExecute(this);
        networkTask.execute(NetworkConstants.CHECK_VERSION);

    }

    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {
        if (object != null && !object.isEmpty()) {

            try {
                JSONObject main = new JSONObject(object);
                if (main.has("status") && main.getString("status").equalsIgnoreCase("success")) {
                    int versionCode = main.getInt("version");

                    if (versionCode > BuildConfig.VERSION_CODE) {
                        /// OPEN UPDATE DIALOG
                        final Dialog dialog = new Dialog(SplashActivity.this);
                        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                        dialog.setContentView(R.layout.ask_retailer_dialog);
                        dialog.getWindow().setLayout(LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
                        dialog.setCancelable(false);
                        TextView tvMessage = (TextView) dialog.findViewById(R.id.tvDialogMessage);
                        tvMessage.setText("A new version of Gionee Star is available in Play Store, Do you want to update ?");
                        TextView tvRetailerName = (TextView) dialog.findViewById(R.id.tvRetailerName);
                        tvRetailerName.setVisibility(View.GONE);
                        TextView tvDialogYes = (TextView) dialog.findViewById(R.id.tvDialogYes);
                        tvDialogYes.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View v) {
                                finish();
                                try {
                                    startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=" + getPackageName())));
                                } catch (android.content.ActivityNotFoundException e) {
                                    startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/apps/details?id=" + getPackageName())));
                                }
                                dialog.dismiss();

                            }
                        });

                        TextView tvDialogNo = (TextView) dialog.findViewById(R.id.tvDialogNo);
                        tvDialogNo.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View v) {
                                dialog.dismiss();
                                finish();

                            }
                        });

                        dialog.show();


                    } else {
                        enterInApp();
                    }

                } else {
                    enterInApp();
//                    Utils.createToast(this, main.optString("message"));
                }

            } catch (JSONException e) {
                enterInApp();
                e.printStackTrace();
            }


        }
    }

}
