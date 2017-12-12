package com.gionee.gioneeabc.helpers;

import android.content.Context;
import android.content.SharedPreferences;

public class DataStore {
    public static final String MY_PREFS_NAME = "GIONEE_STAR";
    public static final String KEY_LOGGED_IN = "keyLoggedIn";
    public static final String KEY_FCM_TOKEN = "fcmToken";
    public static final String isCredentialsSave = "iscredentials";
    public static final String AUTH_TOKEN = "authToken";
    public static final String PROFILE_PIC = "profilePic";
    public static final String DASHBOARD_IMAGES_COUNT = "dashboardImagesCount";
    static String IS_UPDATE_ICON_VISIBLE = "update_icon";
    public static final String KEY_LAST_LOGIN_TIME = "last_login";

    public static final String USER_EMAIL = "email";
    public static final String USER_PASS = "pass";
    public static final String VIDEO_QUEUE_IDS = "video_queue_ids";

    public static void setVideoQueueIds(Context context, String value) {
        setString(context, VIDEO_QUEUE_IDS, value);
    }

    public static String getVideoQueueIds(Context context) {
        return getString(context, VIDEO_QUEUE_IDS);
    }

    public static void setEmail(Context context, String value) {
        setString(context, USER_EMAIL, value);
    }

    public static String getEmail(Context context, String key) {
        return getString(context, key);
    }


    public static void setPass(Context context, String value) {
        setString(context, USER_PASS, value);
    }

    public static String getPass(Context context, String key) {
        return getString(context, key);
    }


    public static void setDashBoardImagesCount(Context context, int value) {
        setInt(context, PROFILE_PIC, value);
    }

    public static int getDashBoardImagesCount(Context context, String key) {
        return getInt(context, key);
    }


    public static void setProfilePic(Context context, String value) {
        setString(context, PROFILE_PIC, value);
    }

    public static String getProfilePic(Context context, String key) {
        return getString(context, key);
    }


    public static void setAuthToken(Context context, String value) {
        setString(context, AUTH_TOKEN, value);
    }

    public static String getAuthToken(Context context, String key) {
        return getString(context, key);
    }





    public static void setCredentialsSave(Context context, boolean value) {
        setBooleanValue(context, isCredentialsSave, value);
    }

    public static boolean getCredentialsSave(Context context) {
        return getBooleanValue(context, isCredentialsSave);
    }

    public static void setFCMToken(Context context, String value) {
        setString(context, KEY_FCM_TOKEN, value);
    }

    public static String getFCMToken(Context context) {
        return getString(context, KEY_FCM_TOKEN);
    }


    public static void setlastLogin(Context context, String value) {
        setString(context, KEY_LAST_LOGIN_TIME, value);
    }

    public static String getLastLogin(Context context) {
        return getString(context, KEY_LAST_LOGIN_TIME);
    }


    public static void setIsFCMTokenUpdate(Context context, boolean isRequired) {
        setBooleanValue(context, "isRequired", isRequired);

    }

    public static boolean getIsFCMTokenUpdate(Context context) {
        return getBooleanValue(context, "isRequired");
    }

    public static void setUpdatedDate(Context context, String value) {
        setString(context, "update_date", value);
    }

    public static String getUpdatedDate(Context context) {
        SharedPreferences prefs = context.getSharedPreferences(MY_PREFS_NAME,
                context.MODE_PRIVATE);
        return prefs.getString("update_date", "0");
    }

    public static void setDisclaimer(Context context, String value) {
        setString(context, "disclaimer", value);
    }

    public static String getDisclaimer(Context context) {
        SharedPreferences prefs = context.getSharedPreferences(MY_PREFS_NAME,
                context.MODE_PRIVATE);
        return prefs.getString("disclaimer", "");
    }

    public static boolean isLoggedIn(Context context, String key) {
        return getBooleanValue(context, key);
    }

    public static void setLoggedIn(Context context, boolean value) {
        setBooleanValue(context, KEY_LOGGED_IN, value);

    }


    public static void setString(Context context, String key, String value) {
        SharedPreferences.Editor editor = context.getSharedPreferences(
                MY_PREFS_NAME, context.MODE_PRIVATE).edit();
        editor.putString(key, value);
        editor.commit();
    }

    public static String getString(Context context, String key) {
        SharedPreferences prefs = context.getSharedPreferences(MY_PREFS_NAME,
                context.MODE_PRIVATE);
        return prefs.getString(key, "");
    }

    public static void setInt(Context context, String key, int value) {
        SharedPreferences.Editor editor = context.getSharedPreferences(
                MY_PREFS_NAME, context.MODE_PRIVATE).edit();
        editor.putInt(key, value);
        editor.commit();
    }

    public static int getInt(Context context, String key) {
        SharedPreferences prefs = context.getSharedPreferences(MY_PREFS_NAME,
                context.MODE_PRIVATE);
        return prefs.getInt(key, 0);
    }


    public static void setBooleanValue(Context context, String key,
                                       boolean value) {
        SharedPreferences.Editor editor = context.getSharedPreferences(
                MY_PREFS_NAME, context.MODE_PRIVATE).edit();
        editor.putBoolean(key, value);
        editor.commit();
    }

    public static boolean getBooleanValue(Context context, String key) {
        SharedPreferences prefs = context.getSharedPreferences(MY_PREFS_NAME,
                context.MODE_PRIVATE);
        return prefs.getBoolean(key, false);
    }

    public static void clearAllValue(Context context) {

        SharedPreferences settings = context.getSharedPreferences(MY_PREFS_NAME, Context.MODE_PRIVATE);
        settings.edit().clear().commit();


    }
}
