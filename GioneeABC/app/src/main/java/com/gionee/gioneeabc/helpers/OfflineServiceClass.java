package com.gionee.gioneeabc.helpers;

import android.content.Context;
import android.database.Cursor;

import com.gionee.gioneeabc.bean.UserBean;
import com.gionee.gioneeabc.database.DataBaseHandler;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by Linchpin
 */
public class OfflineServiceClass implements NetworkTaskNew.Result {
    private static final int ID_LOGOUT = 101;
    private static final int AUDIT = 102;
    private static final int SET_READ = 103;
    private static Context context;
    private static OfflineServiceClass offlineServiceClass;

    private OfflineServiceClass() {
    }

    public static OfflineServiceClass getInstance(Context contxt) {
        context = contxt;
        if (offlineServiceClass == null) {
            offlineServiceClass = new OfflineServiceClass();
        }
        return offlineServiceClass;
    }

    public void onHitLogoutWebService(boolean isProgressDialog) {
        if (Util.isNetworkAvailable(context)) {
            DataBaseHandler databaseHandler = DataBaseHandler.getInstance(context);
            Cursor cursor = databaseHandler.getAllSubmitLogoutData();
            if (cursor.moveToFirst()) {
                do {
                    String requestLogout = cursor.getString(cursor.getColumnIndex(DataBaseHandler.COL_SUBMIT_DATA));

                    NetworkTaskNew networkTask = new NetworkTaskNew(context, ID_LOGOUT);
                    networkTask.setProgressDialog(isProgressDialog);
                    networkTask.exposePostExecute(this);
                    networkTask.execute(new String[]{NetworkConstants.LOGOUT_URL, requestLogout});
                } while (cursor.moveToNext());
            }
        }
    }

    public void updateAuditTrailDataOnServer() {
        if (!auditJsonData().equalsIgnoreCase("") && !DataStore.getAuthToken(context, DataStore.AUTH_TOKEN).equals("")) {
            NetworkTaskNew networkTask = new NetworkTaskNew(context, AUDIT);
            networkTask.setProgressDialog(false);
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
            UserBean user = DataBaseHandler.getInstance(context).getUser();
            Cursor cursor = DataBaseHandler.getInstance(context).getAllAuditTrailData(user.getUserId());
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
                Obj.put("access_token", DataStore.getAuthToken(context, DataStore.AUTH_TOKEN));
                Obj.put("user_id", user.getUserId());

                String temp = Obj.toString();
                return temp;
            } else return "";
        } catch (Exception ex) {
            return "";
        }
    }

    public void readUpdateStatusOnServer() {
        if (!readJsonData().equalsIgnoreCase("") && !DataStore.getAuthToken(context, DataStore.AUTH_TOKEN).equals("")) {
            NetworkTaskNew networkTask = new NetworkTaskNew(context, SET_READ);
            networkTask.setProgressDialog(false);
            networkTask.exposePostExecute(this);
            networkTask.execute(NetworkConstants.SET_CATEGORY_READ_URL, readJsonData());
        }
    }

    private String readJsonData() {
        try {
            boolean isDataAvilable = false;
            JSONObject Obj = new JSONObject();
            JSONArray jsonArray = new JSONArray();
            JSONObject jsonObject;
            UserBean user = DataBaseHandler.getInstance(context).getUser();
            Cursor cursor = DataBaseHandler.getInstance(context).getAllTopicReadData(user.getUserId());
            if (cursor.moveToFirst()) {
                do {
                    String category_id = cursor.getString(cursor.getColumnIndex(DataBaseHandler.CATEGORY_ID));
                    String subcategory_id = cursor.getString(cursor.getColumnIndex(DataBaseHandler.SUB_CATEGORY_ID));
                    String topic_id = cursor.getString(cursor.getColumnIndex(DataBaseHandler.TOPIC_ID));
                    jsonObject = new JSONObject();
                    jsonObject.put("category_id", category_id);
                    jsonObject.put("subcategory_id", subcategory_id);
                    jsonObject.put("topic_id", topic_id);
                    jsonArray.put(jsonObject);
                    isDataAvilable = true;
                } while (cursor.moveToNext());
            }
            if (isDataAvilable) {
                Obj.put("data", jsonArray);
                Obj.put("access_token", DataStore.getAuthToken(context, DataStore.AUTH_TOKEN));
                Obj.put("user_id", user.getUserId());

                String temp = Obj.toString();
                return temp;
            } else return "";
        } catch (Exception ex) {
            return "";
        }
    }

    @Override
    public void resultFromNetwork(String object, int id, int arg1, String arg2) throws JSONException {
        if (object != null && !object.isEmpty()) {
            if (id == ID_LOGOUT) {
                try {
                    JSONObject jsonObject = new JSONObject(object);
                    if ((jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {
                        DataBaseHandler.getInstance(context).deleteSubmitLogout();
//                        Util.logout((Activity) context);
                    } /*else
                        Util.createToast(context, "Please try again..");*/

                } catch (Exception ex) {

                }
            } else if (id == AUDIT) {
                try {
                    JSONObject jsonObject = new JSONObject(object);
                    if ((jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {
                        UserBean user = DataBaseHandler.getInstance(context).getUser();
                        DataBaseHandler.getInstance(context).deleteAllAuditTrailData(user.getUserId());
                    }
                } catch (Exception ex1) {

                }

            } else if (id == SET_READ) {
                try {
                    JSONObject jsonObject = new JSONObject(object);
                    if ((jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {
                        UserBean user = DataBaseHandler.getInstance(context).getUser();
                        DataBaseHandler.getInstance(context).deleteAllREeadData(user.getUserId());
                    }
                } catch (Exception ex1) {

                }
            }
        }
    }
}
