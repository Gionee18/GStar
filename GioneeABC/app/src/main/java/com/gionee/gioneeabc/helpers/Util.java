package com.gionee.gioneeabc.helpers;

import android.app.Activity;
import android.app.NotificationManager;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Point;
import android.graphics.Typeface;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.os.StatFs;
import android.support.design.widget.Snackbar;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentTransaction;
import android.support.v7.app.AlertDialog;
import android.text.TextUtils;
import android.util.Log;
import android.view.Display;
import android.view.View;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.Toast;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.LoginActivity;
import com.gionee.gioneeabc.bean.RecommAttribBean;
import com.gionee.gioneeabc.bean.RecommNonGioneeModelBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.google.gson.Gson;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.mime.content.FileBody;
import org.apache.http.entity.mime.content.StringBody;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.ByteArrayBuffer;
import org.apache.http.util.EntityUtils;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.File;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.URL;
import java.net.URLConnection;
import java.nio.charset.Charset;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.List;

/**
 * Created by Linchpin25 on 1/20/2016.
 */
public class Util {


    private static int screenHeight = 0;
    private static Toast toast = null;


    public static int getScreenHeight(Context c) {
        if (screenHeight == 0) {
            WindowManager wm = (WindowManager) c.getSystemService(Context.WINDOW_SERVICE);
            Display display = wm.getDefaultDisplay();
            Point size = new Point();
            display.getSize(size);
            screenHeight = size.y;
        }

        return screenHeight;
    }


    public static boolean isNetworkAvailable(Context context) {
        ConnectivityManager connectivityManager = (ConnectivityManager) context
                .getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo activeNetworkInfo = connectivityManager
                .getActiveNetworkInfo();
        return activeNetworkInfo != null && activeNetworkInfo.isConnected();
    }




    /*
    *
    * E-MAIL VALIDATOR METHOD
    *
    * */

    public final static boolean isValidEmail(CharSequence target) {
        if (TextUtils.isEmpty(target)) {
            return false;
        } else {
            return android.util.Patterns.EMAIL_ADDRESS.matcher(target).matches();
        }
    }



    /*
    * CHECKING AVAILABLE INTERNAL MEMORY SIZE
    *
    * */

    public static long getAvailableInternalMemorySize() {
        File path = Environment.getDataDirectory();
        StatFs stat = new StatFs(path.getPath());
        long blockSize = stat.getBlockSize();
        long availableBlocks = stat.getAvailableBlocks();
        return availableBlocks * blockSize;
    }

    /*
    * CHECKING AVAILABLE EXTERNAL MEMORY SIZE
    *
    * */

    public static String getAvailableExternalMemorySize() {
        if (externalMemoryAvailable()) {
            File path = Environment.getExternalStorageDirectory();
            StatFs stat = new StatFs(path.getPath());
            long blockSize = stat.getBlockSize();
            long availableBlocks = stat.getAvailableBlocks();
            return formatSize(availableBlocks * blockSize);
        } else {
            return "Unable to detect memory card";
        }
    }

    /*
    * METHOD TO CHECK IF EXTERNAL MEMORY IS AVAILABLE
    *
    * */

    public static boolean externalMemoryAvailable() {
        return Environment.getExternalStorageState().equals(
                Environment.MEDIA_MOUNTED);
    }



    /*
    * METHOD FOR FORMATTING MEMORY SIZE
    *
    * */

    public static String formatSize(long size) {
        String suffix = null;

        if (size >= 1024) {
            suffix = "KB";
            size /= 1024;
            if (size >= 1024) {
                suffix = "MB";
                size /= 1024;
            }
        }

        StringBuilder resultBuffer = new StringBuilder(Long.toString(size));

        int commaOffset = resultBuffer.length() - 3;
        while (commaOffset > 0) {
            resultBuffer.insert(commaOffset, ',');
            commaOffset -= 3;
        }

        if (suffix != null) resultBuffer.append(suffix);
        return resultBuffer.toString();
    }



/*
*  SET TYPEFACE
*
*
* */

    public static Typeface getRoboMedium(Context context) {
        return Typeface.createFromAsset(context.getAssets(), "font/Roboto-Medium.ttf");
    }

    public static Typeface getRoboRegular(Context context) {
        return Typeface.createFromAsset(context.getAssets(), "font/Roboto-Regular.ttf");
    }

/*
*  CREATE SNACKBAR
*
* */


    public static void createSnackBar(View view, String message) {
        Snackbar.make(view, message, Snackbar.LENGTH_SHORT).show();
    }

/*
*  CREATE TOAST MESSAGES
*
* */


    public static void createToast(Context mContext, String msg) {

        if (toast != null)
            toast.cancel();


        toast = Toast.makeText(mContext, msg, Toast.LENGTH_SHORT);
        toast.show();

    }


/*
*  METHOD FOR SENDING IMAGE TO SERVER
*
* */


    public static String sendRequestImageToServer(final String url, List<NameValuePair> params, File fileParts) {
        String serverResponse = null;
        final Charset chars = Charset.forName("UTF-8");

        try {
            HttpClient client = new DefaultHttpClient();
            HttpPost httppost = new HttpPost(url);

            AndroidMultiPartEntity multipartEntity = new AndroidMultiPartEntity(new AndroidMultiPartEntity.ProgressListener() {

                @Override
                public void transferred(long num) {

                }
            });
            for (int i = 0; i < params.size(); i++) {
                multipartEntity.addPart(params.get(i).getName(), new StringBody(params.get(i).getValue()));
            }
            multipartEntity.addPart("image", new FileBody(fileParts));
            // httppost.setEntity(new UrlEncodedFormEntity(params));
            httppost.setEntity(multipartEntity);

            HttpResponse response = client.execute(httppost);
            HttpEntity resEntity = response.getEntity();

            if (resEntity != null) {
                serverResponse = EntityUtils.toString(resEntity);
            }
            return serverResponse;

        } catch (Exception e) {

            e.printStackTrace();
        }

        return serverResponse;

    }

/*
*  METHOD FOR SENDING  POST REQUEST
*
* */

    public static String httpPostRaw(String params,
                                     List<NameValuePair> namevalueList, List<NameValuePair> headerList) {
        InputStream is = null;
        String result = "";
        try {
//            HttpClient httpclient = new DefaultHttpClient();
            HttpClient httpclient = new DefaultHttpClient();
            HttpPost httpPost = new HttpPost(params);
            if (headerList != null && headerList.size() > 0) {
                for (int i = 0; i < headerList.size(); i++) {
                    httpPost.setHeader(headerList.get(i).getName(), headerList.get(i).getValue());
                }
            }
            httpPost.setEntity(new UrlEncodedFormEntity(namevalueList));
            HttpResponse response = httpclient.execute(httpPost);
            HttpEntity entity = response.getEntity();
            is = entity.getContent();
        } catch (Exception e) {
            Log.e("log_tag", "Error in http connection " + e.toString());
        }

        // convert response to string
        try {
            BufferedReader reader = new BufferedReader(new InputStreamReader(
                    is, "UTF-8"), 8);
            StringBuilder sb = new StringBuilder();
            String line = null;
            while ((line = reader.readLine()) != null) {
                sb.append(line + "\n");
            }
            is.close();
            result = sb.toString();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return result;

    }


    /*
*  METHOD FOR SENDING  GET   REQUEST
*
* */


    public static String httpGetRaw(String url, List<NameValuePair> headerList) {
        InputStream is = null;
        String result = "";
        try {
            HttpClient httpclient = new DefaultHttpClient();
            HttpGet httpget = new HttpGet(url);
            if (headerList != null && headerList.size() > 0) {
                for (int i = 0; i < headerList.size(); i++) {
                    httpget.setHeader(headerList.get(i).getName(), headerList.get(i).getValue());
                }
            }

            HttpResponse response = httpclient.execute(httpget);
            is = response.getEntity().getContent();
        } catch (Exception e) {
            Log.e("log_tag", "Error in http connection " + e.toString());
        }

        // convert response to string
        try {
            BufferedReader reader = new BufferedReader(new InputStreamReader(
                    is, "UTF-8"), 8);
            StringBuilder sb = new StringBuilder();
            String line = null;
            while ((line = reader.readLine()) != null) {
                sb.append(line + "\n");
            }
            is.close();
            result = sb.toString();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return result;

    }

    public static String getCurrentDate() {
        try {
            Calendar cal = Calendar.getInstance();
            SimpleDateFormat format1 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
            System.out.println(cal.getTime());
            String formatted = format1.format(cal.getTime());
            formatted.replace(" ", "%20");
            return formatted;
        } catch (Exception e) {
            e.printStackTrace();
        }
        return "";
    }

    public static boolean checkImageIAlreadyExist(String imageName) {
        try {
            File[] listFile;
            File file = new File(Environment.getExternalStorageDirectory(), NetworkConstants.hideFolderFromGallery + "GioneeStar");
            if (file.isDirectory()) {
                listFile = file.listFiles();
                for (int i = 0; i < listFile.length; i++) {
                    if (listFile[i].getName().equals(imageName)) ;
                    return true;
                }
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    public static void deleteFolderContent() {
        try {
            File[] listFile;
            File file = new File(Environment.getExternalStorageDirectory(), NetworkConstants.hideFolderFromGallery + "GioneeStar");
            if (file.isDirectory()) {
                listFile = file.listFiles();
                for (int i = 0; i < listFile.length; i++) {
                    listFile[i].delete();

                }
            }

        } catch (Exception e) {
            e.printStackTrace();
        }

    }

    public static byte[] getLogoImage(String url) {
        try {
            URL imageUrl = new URL(url);
            URLConnection ucon = imageUrl.openConnection();

            InputStream is = ucon.getInputStream();
            BufferedInputStream bis = new BufferedInputStream(is);

            ByteArrayBuffer baf = new ByteArrayBuffer(500);
            int current = 0;
            while ((current = bis.read()) != -1) {
                baf.append((byte) current);
            }

            return baf.toByteArray();
        } catch (Exception e) {
            Log.d("ImageManager", "Error: " + e.toString());
        }
        return null;
    }

    public static String setFontInText(String text) {
        String s = "<html>\n" +
                "<head>\n" +
                "<style type=\"text/css\">\n" +
                "@font-face {\n" +
                "    font-family: MyFont;\n" +
                "    src: url(\"file:///android_asset/font/HelveticaNeueLTStd_ThEx.ttf\")" +
                "}\n" +
                "pre,p,body {\n" +
                "    font-family: MyFont;" +
                "}\n" +
                "</style>\n" +
                "</head>\n" +
                "<body><p>" +
                text +
                "</p></body>\n" +
                "</html>";
        return s;
    }

    public static void openUrl(Context context, String url) {
        Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
        context.startActivity(browserIntent);
    }

    public static void watchYoutubeVideo(Context context, String video_path) {

        Intent appIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(video_path));
        Intent webIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(video_path));
        try {
            //   startActivity(appIntent);
        } catch (ActivityNotFoundException ex) {
            //   startActivity(webIntent);
        }
    }

    public static void logoutwithMessage(final Activity activity) {

        final DataBaseHandler dataBaseHandler = new DataBaseHandler(activity);

        AlertDialog.Builder builder = new AlertDialog.Builder(activity);
        builder.setTitle("Session Expire !");
        builder.setMessage("Please login again to continue..")
                .setCancelable(false)
                .setPositiveButton("OK", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        dialog.dismiss();
                        NotificationManager notifManager = (NotificationManager) activity.getSystemService(Context.NOTIFICATION_SERVICE);
                        notifManager.cancelAll();

                        dataBaseHandler.deleteAllTable();
                        String token = DataStore.getFCMToken(activity);
                        String acessToken = DataStore.getAuthToken(activity, DataStore.AUTH_TOKEN);
                        String user = "", pass = "";
                        if (DataStore.getCredentialsSave(activity)) {
                            user = DataStore.getEmail(activity, DataStore.USER_EMAIL);
                            pass = DataStore.getPass(activity, DataStore.USER_PASS);
                        }

                        DataStore.clearAllValue(activity);

                        DataStore.setFCMToken(activity, token);
                        DataStore.setAuthToken(activity, acessToken);

                        if (!user.equals("")) {
                            DataStore.setEmail(activity, user);
                            DataStore.setPass(activity, pass);
                            DataStore.setCredentialsSave(activity, true);
                        }

                        activity.finishAffinity();

                        activity.startActivity(new Intent(activity, LoginActivity.class));

                    }
                });
        AlertDialog alert = builder.create();
        alert.show();
    }

    public static void logout(final Activity activity) {

        final DataBaseHandler dataBaseHandler = new DataBaseHandler(activity);

        NotificationManager notifManager = (NotificationManager) activity.getSystemService(Context.NOTIFICATION_SERVICE);
        notifManager.cancelAll();

        dataBaseHandler.deleteAllTable();
        String token = DataStore.getFCMToken(activity);
        String acessToken = DataStore.getAuthToken(activity, DataStore.AUTH_TOKEN);
        String user = "", pass = "";
        if (DataStore.getCredentialsSave(activity)) {
            user = DataStore.getEmail(activity, DataStore.USER_EMAIL);
            pass = DataStore.getPass(activity, DataStore.USER_PASS);
        }

        DataStore.clearAllValue(activity);

        if (!user.equals("")) {
            DataStore.setEmail(activity, user);
            DataStore.setPass(activity, pass);
            DataStore.setCredentialsSave(activity, true);
        }
        DataStore.setFCMToken(activity, token);
        DataStore.setAuthToken(activity, acessToken);
        activity.finishAffinity();

        activity.startActivity(new Intent(activity, LoginActivity.class));

    }


    public static void hideKeyBoard(Activity activity) {
        View view = activity.getCurrentFocus();
        if (view != null) {
            InputMethodManager imm = (InputMethodManager) activity.getSystemService(Context.INPUT_METHOD_SERVICE);
            imm.hideSoftInputFromWindow(view.getWindowToken(), 0);
        }
    }

    public static void performFragmentTransaction(FragmentActivity activity, int containerViewId, String fragmentTag, boolean addToBackStack, Bundle data, Fragment fragment) {
        FragmentTransaction fragmentTransaction = activity.getSupportFragmentManager().beginTransaction();
        if (data != null)
            fragment.setArguments(data);
        fragmentTransaction.setCustomAnimations(R.anim.slide_in_left, R.anim.slide_out_right, R.anim.slide_in_right, R.anim.slide_out_left);
        fragmentTransaction.replace(containerViewId, fragment, fragmentTag);
        if (addToBackStack)
            fragmentTransaction.addToBackStack(fragmentTag);
        fragmentTransaction.commit();
    }

    public static boolean isBrandModelSelected(Context context) {
        boolean isSelected = false;
        Cursor cursor = DataBaseHandler.getInstance(context).getAllRecommModelData();
        if (cursor.moveToFirst()) {
            String responseModel = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_GET_DATA));
            if (!responseModel.isEmpty()) {
                RecommNonGioneeModelBean recommNonGioneeModelBean1 = new Gson().fromJson(responseModel, RecommNonGioneeModelBean.class);
                List<RecommNonGioneeModelBean.RecommNonGioneeModeData> brandNameList = recommNonGioneeModelBean1.getData();
                if (brandNameList.size() > 0) {
                    for (int i = 0; i < brandNameList.size(); i++) {
                        if (brandNameList.get(i).isSelected()) {
                            isSelected = true;
                            break;
                        }
                    }
                }
            }
        }
        return isSelected;
    }

    public static boolean isAttribSelected(Context context) {
        boolean isSelected = false;
        Cursor cursor1 = DataBaseHandler.getInstance(context).getAllRecommAttribData();
        if (cursor1.moveToFirst()) {
            String attribResponse = cursor1.getString(cursor1.getColumnIndex(DataBaseHandler.COL_GET_DATA));
            RecommAttribBean recommAttribBean = new Gson().fromJson(attribResponse, RecommAttribBean.class);
            List<RecommAttribBean.RecommAttribData> recommAttribDataList = recommAttribBean.getData();
            if (recommAttribDataList.size() > 0) {
                for (int i = 0; i < recommAttribDataList.size(); i++) {
                    final RecommAttribBean.RecommAttribData recommAttribData = recommAttribDataList.get(i);
                    List<String> selFilterListAttrib = recommAttribData.getSelSearchAttrib();
                    if (selFilterListAttrib != null && selFilterListAttrib.size() > 0) {
                        isSelected = true;
                        break;
                    }
                }
            }
        }
        return isSelected;
    }

}
