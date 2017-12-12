package com.gionee.gioneeabc.adapters;

import android.app.Activity;
import android.app.DownloadManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.database.Cursor;
import android.net.Uri;
import android.os.Environment;
import android.provider.Settings;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.activities.ChangePasswordActivity;
import com.gionee.gioneeabc.activities.EditProfileScreen;
import com.gionee.gioneeabc.activities.MainActivity;
import com.gionee.gioneeabc.activities.ProductsActivity;
import com.gionee.gioneeabc.activities.RecomenderActivity;
import com.gionee.gioneeabc.activities.TutorialProductsActivity;
import com.gionee.gioneeabc.activities.UpdateActivity;
import com.gionee.gioneeabc.bean.UserBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.fragments.HomeFragment;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.FontImageView;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTaskNew;
import com.gionee.gioneeabc.helpers.UIUtils;
import com.gionee.gioneeabc.helpers.Util;
import com.nostra13.universalimageloader.core.DisplayImageOptions;
import com.nostra13.universalimageloader.core.ImageLoader;
import com.nostra13.universalimageloader.core.ImageLoaderConfiguration;
import com.nostra13.universalimageloader.core.display.RoundedBitmapDisplayer;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.File;

/**
 * Created by Linchpin25 on 1/20/2016.
 */

public class NavigationDrawerAdapter extends RecyclerView.Adapter<NavigationDrawerAdapter.ViewHolder> implements MainActivity.UnRegisterReceiver, NetworkTaskNew.Result {

    private static final int LOGOUT = 101;
    private static final int AUDIT = 102;
    public String[] drawerElements;
    public int[] drawerElementsIconsBack;
    Context context;
    private static final int TYPE_HEADER = 0;
    private static final int TYPE_ELEMENT = 1;
    int selected;
    boolean isFirst = true;
    String[] drawerElementsIcons;
    public static DataBaseHandler dbHandler;
    public static UserBean user;
    String url;
    DownloadManager downloadmanager;
    BroadcastReceiver receiver;
    long enqueue;
    ImageLoader imageLoader;
    DisplayImageOptions options;
    TextView userName;
    ImageView userImage;
    private static final int SET_READ = 103;

    public NavigationDrawerAdapter(Context context, String[] drawerElements, String[] drawerElementsIcons, int[] drawerElementsIconsBack) {
        this.drawerElements = drawerElements;
        this.context = context;
        this.drawerElementsIcons = drawerElementsIcons;
        this.drawerElementsIconsBack = drawerElementsIconsBack;
        dbHandler = DataBaseHandler.getInstance(context);


        downloadmanager = (DownloadManager) context.getSystemService(Context.DOWNLOAD_SERVICE);


        imageLoader = ImageLoader.getInstance();
        ImageLoaderConfiguration config = new ImageLoaderConfiguration.Builder(context)
                .memoryCacheSize(41943040)
                .discCacheSize(104857600)
                .threadPoolSize(10)
                .build();
        imageLoader.init(config);
        options = new DisplayImageOptions.Builder()
                .cacheInMemory(true)
                .cacheOnDisk(true)
                .showStubImage(R.drawable.default_user)
                .showImageOnFail(R.drawable.default_user)
                .considerExifParams(true)
                .displayer(new RoundedBitmapDisplayer(120))
                .build();


        receiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                String action = intent.getAction();
                if (DownloadManager.ACTION_DOWNLOAD_COMPLETE.equals(action)) {
                    long downloadId = intent.getLongExtra(
                            DownloadManager.EXTRA_DOWNLOAD_ID, 0);
                    DownloadManager.Query query = new DownloadManager.Query();
                    query.setFilterById(enqueue);
                    Cursor c = downloadmanager.query(query);
                    if (c.moveToFirst()) {
                        int columnIndex = c
                                .getColumnIndex(DownloadManager.COLUMN_STATUS);
                        if (DownloadManager.STATUS_SUCCESSFUL == c
                                .getInt(columnIndex)) {
                            //  Util.createToast(context, "Download completed... Please check your data in Requester Document folder");
                            dbHandler.addUserProfileImage(user.getUserId(), Environment.getExternalStorageDirectory() + "/GioneeStar/" + user.getUserImage());
                        }

                    }
                }
            }
        };

        context.registerReceiver(receiver, new IntentFilter(
                DownloadManager.ACTION_DOWNLOAD_COMPLETE));
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        if (viewType == TYPE_HEADER) {
            View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.navigation_header, parent, false); //Inflating the layout

            ViewHolder vhItem = new ViewHolder(v, viewType); //Creating ViewHolder and passing the object of type view
            return vhItem;
        } else if (viewType == TYPE_ELEMENT) {
            View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.navigation_element, parent, false); //Inflating the layout

            ViewHolder vhItem = new ViewHolder(v, viewType); //Creating ViewHolder and passing the object of type view
            return vhItem;

        }
        return null;
    }

    @Override
    public void unregister() {
        try {
            context.unregisterReceiver(receiver);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {
        if (holder.holderId == 0) {

            user = dbHandler.getUser();

            if (user != null) {
                userName = holder.tvUserName;
                holder.tvUserName.setText(user.getUserName());
                holder.tvUserName.setTypeface(Util.getRoboMedium(context));
                holder.tvUserEmail.setText(user.getUserEmail());
                holder.tvUserEmail.setTypeface(Util.getRoboMedium(context));
                userImage = holder.ivIcon;
                if (user.getUserImageLocalUrl() == null || user.getUserImageLocalUrl().equals("") || !(new File(user.getUserImageLocalUrl())).exists()) {
                    imageLoader.displayImage(NetworkConstants.BASE_URL + user.getUserImageServerUrl() + "/" + user.getUserImage(), holder.ivIcon, options, null);
                    fileDownload(user);

                } else {
                    String imageUrl = "file:///" + user.getUserImageLocalUrl();
                    imageLoader.displayImage(imageUrl, holder.ivIcon, options, null);

                }


            }
        } else if (holder.holderId == 1) {
            holder.tvElement.setText(drawerElements[position - 1]);
            holder.tvElement.setTypeface(Util.getRoboMedium(context));
            holder.ivIconBack.setTextColor(context.getResources().getColor(drawerElementsIconsBack[position - 1]));
            holder.ivElementIcon.setText(drawerElementsIcons[position - 1]);
        }
    }

    @Override
    public int getItemCount() {
        return drawerElements.length + 1;
    }


    @Override
    public int getItemViewType(int position) {
        if (position == 0)
            return TYPE_HEADER;
        else
            return TYPE_ELEMENT;
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        TextView tvUserName, tvUserEmail, tvElement;
        FontImageView ivIconBack, ivElementIcon;
        ImageView ivIcon;
        int holderId;


        public ViewHolder(View itemView, final int viewType) {
            super(itemView);
            if (isFirst) {
                displayView(1);
                isFirst = false;
            }


            if (viewType == TYPE_HEADER) {
                tvUserName = (TextView) itemView.findViewById(R.id.tv_name);
                tvUserEmail = (TextView) itemView.findViewById(R.id.tv_email);
                ivIcon = (ImageView) itemView.findViewById(R.id.iv_icon);
                holderId = 0;
            } else if (viewType == TYPE_ELEMENT) {

                itemView.setClickable(true);

                itemView.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {


                        selected = getAdapterPosition();
                        displayView(getAdapterPosition());
                        MainActivity.drawerLayout.closeDrawers();
                        notifyDataSetChanged();
                    }

                });

                holderId = 1;
                tvElement = (TextView) itemView.findViewById(R.id.tv_element);
                ivElementIcon = (FontImageView) itemView.findViewById(R.id.iv_icon);
                ivIconBack = (FontImageView) itemView.findViewById(R.id.iv_icon_back);

            }


        }


    }


    public void displayView(int position) {
        Fragment fragment = null;
        String title = "";
        if (position == 1) {
            fragment = new HomeFragment();
            if (fragment != null) {
                FragmentManager fragmentManager = MainActivity.fragmentManager;
                FragmentTransaction fragmentTransaction = fragmentManager.beginTransaction();
                fragmentTransaction.replace(R.id.container, fragment);
                fragmentTransaction.commit();
            }
        } else if (position == 2) {
            context.startActivity(new Intent(context, ProductsActivity.class));
        } else if (position == 3) {
            UIUtils.isFilterFromProduct = false;
            Intent intent = new Intent(context, RecomenderActivity.class);
            intent.putExtra(UIUtils.RECOMM_KEY_FILTER_TYPE, UIUtils.RECOMM_VALUE_FILTER_MANUFACTURER);
            if (Util.isBrandModelSelected(context))
                intent.putExtra(UIUtils.RECOMM_KEY_FROM, UIUtils.RECOMM_FROM_VALUE_FILTER);
            else
                intent.putExtra(UIUtils.RECOMM_KEY_FROM, UIUtils.RECOMM_FROM_VALUE_MAIN);
            context.startActivity(intent);
        } else if (position == 4) {
            context.startActivity(new Intent(context, TutorialProductsActivity.class));
        } else if (position == 5) {
            context.startActivity(new Intent(context, UpdateActivity.class));
        } else if (position == 6) {
            context.startActivity(new Intent(context, EditProfileScreen.class));
        } else if (position == 7) {
            context.startActivity(new Intent(context, ChangePasswordActivity.class));
        } else if (position == 8) {
            Intent i = new Intent(Intent.ACTION_VIEW, Uri.parse("http://103.20.213.33/gstar"));
            context.startActivity(i);
        } else if (position == 9) {
            readUpdateStatusOnServer();
            if (!auditJsonData().equalsIgnoreCase("") && Util.isNetworkAvailable(context)) {
                NetworkTaskNew networkTask = new NetworkTaskNew(context, AUDIT);
                networkTask.exposePostExecute(this);
                networkTask.execute(NetworkConstants.AUDIT_TRAIL, auditJsonData());
            } else {
                String device_id = Settings.Secure.getString(context.getContentResolver(), Settings.Secure.ANDROID_ID);
                JSONObject jsonObject = new JSONObject();
                try {
                    jsonObject.put("access_token", DataStore.getAuthToken(context, DataStore.AUTH_TOKEN));
                    jsonObject.put("device_id", device_id);
                    jsonObject.put("login_time", DataStore.getLastLogin(context));
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                if (Util.isNetworkAvailable(context)) {

                    NetworkTaskNew networkTask = new NetworkTaskNew(context, LOGOUT);
                    networkTask.exposePostExecute(this);
                    networkTask.execute(NetworkConstants.LOGOUT_URL, jsonObject.toString());
                } else {
                    DataBaseHandler.getInstance(context).addSubmitData(jsonObject.toString(), DataBaseHandler.TYPE_LOGOUT);
                    try {
                        Util.logout((MainActivity) context);
                    } catch (Exception e) {
                    }
                }
            }


        }
        MainActivity.drawerLayout.closeDrawers();
    }

    private String auditJsonData() {
        try {
            boolean isDataAvilable = false;
            JSONObject Obj = new JSONObject();
            JSONArray jsonArray = new JSONArray();
            JSONObject jsonObject;

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

    public void fileDownload(UserBean user) {
        if (!Util.checkImageIAlreadyExist(user.getUserImage())) {
            File direct = new File(Environment.getExternalStorageDirectory()
                    + NetworkConstants.hideFolderFromGallery + "GioneeStar");
            if (!direct.exists()) {
                direct.mkdirs();
            }
            try {
                url = NetworkConstants.BASE_URL + user.getUserImageServerUrl() + "/" + user.getUserImage();
                Uri downloadUri = Uri.parse(url);
                DownloadManager.Request request = new DownloadManager.Request(
                        downloadUri);

                request.setAllowedNetworkTypes(
                        DownloadManager.Request.NETWORK_WIFI
                                | DownloadManager.Request.NETWORK_MOBILE)
                        .setAllowedOverRoaming(false)
                        .setTitle("GioneeStar")
                        .setNotificationVisibility(DownloadManager.Request.VISIBILITY_HIDDEN)
                        .setDestinationInExternalPublicDir(NetworkConstants.hideFolderFromGallery + "GioneeStar", NetworkConstants.hideImageFromGallery + user.getUserImage());

                enqueue = downloadmanager.enqueue(request);


            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    @Override
    public void resultFromNetwork(String object, int id, int arg1, String arg2) throws JSONException {
        switch (id) {
            case LOGOUT: {
                try {
                    JSONObject jsonObject = new JSONObject(object);
                    if ((jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {
                        Util.logout((Activity) context);
                    } else
                        Util.createToast(context, "Please try again..");

                } catch (Exception ex) {

                }
                break;
            }
            case AUDIT: {
                try {
                    JSONObject jsonObject = new JSONObject(object);
                    JSONObject logoutObject = new JSONObject();
                    try {
                        String device_id = Settings.Secure.getString(context.getContentResolver(), Settings.Secure.ANDROID_ID);
                        logoutObject.put("access_token", DataStore.getAuthToken(context, DataStore.AUTH_TOKEN));
                        logoutObject.put("device_id", device_id);
                        logoutObject.put("login_time", DataStore.getLastLogin(context));
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                    if ((jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {

                        DataBaseHandler.getInstance(context).deleteAllAuditTrailData(user.getUserId());
                        NetworkTaskNew networkTask = new NetworkTaskNew(context, LOGOUT);
                        networkTask.exposePostExecute(this);
                        networkTask.execute(NetworkConstants.LOGOUT_URL, logoutObject.toString());
                    } else {
                        NetworkTaskNew networkTask = new NetworkTaskNew(context, LOGOUT);
                        networkTask.exposePostExecute(this);
                        networkTask.execute(NetworkConstants.LOGOUT_URL, logoutObject.toString());
                    }
                } catch (Exception ex1) {

                }
                break;
            }
            case SET_READ: {
                try {
                    JSONObject jsonObject = new JSONObject(object);
                    if ((jsonObject.has("status") && jsonObject.optString("status").toString().equalsIgnoreCase("success"))) {
                        UserBean user = DataBaseHandler.getInstance(context).getUser();
                        DataBaseHandler.getInstance(context).deleteAllREeadData(user.getUserId());
                    }
                } catch (Exception ex1) {

                }
                break;

            }
        }
    }
}
