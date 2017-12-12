package com.gionee.gioneeabc.activities;

import android.app.Dialog;
import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.provider.MediaStore;
import android.support.design.widget.CoordinatorLayout;
import android.support.design.widget.FloatingActionButton;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import com.gionee.gioneeabc.R;
import com.gionee.gioneeabc.adapters.CountryAdeptor;
import com.gionee.gioneeabc.bean.NDListBean;
import com.gionee.gioneeabc.bean.UserBean;
import com.gionee.gioneeabc.database.DataBaseHandler;
import com.gionee.gioneeabc.helpers.DataStore;
import com.gionee.gioneeabc.helpers.NetworkConstants;
import com.gionee.gioneeabc.helpers.NetworkTask;
import com.gionee.gioneeabc.helpers.Util;
import com.google.gson.Gson;
import com.nostra13.universalimageloader.core.DisplayImageOptions;
import com.nostra13.universalimageloader.core.ImageLoader;
import com.nostra13.universalimageloader.core.ImageLoaderConfiguration;
import com.nostra13.universalimageloader.core.display.RoundedBitmapDisplayer;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.File;
import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Locale;

/**
 * Created by Linchpin25 on 3/7/2016.
 */
public class EditProfileScreen extends AppCompatActivity implements NetworkTask.Result, View.OnClickListener {

    private static final String TRAINER_ROLE = "10";
    private static final String ADMIN_ROLE = "20";
    private static final String SUPER_ADMIN_ROLE = "05";
    EditText etFirstName, etLastName, etEmail, etContact;
    TextView etND, etRDS, etZone, etCity, etState;
    NetworkTask networkTask;
    final int GET_USER_INFO = 101, SET_USER_INFO = 102, GET_ND_LIST = 103, GET_RDS_LIST = 104, GET_ZONE_LIST = 105, GET_STATE = 106, GET_CITY = 107;
    ImageLoader imageLoader;
    DisplayImageOptions options;
    ImageView ivProfilePic, ivSave;
    DataBaseHandler dbHandler;
    UserBean user;
    private static final int CAMERA_CAPTURE_IMAGE_REQUEST_CODE = 100, RESULT_LOAD = 200;
    private Uri fileUri;
    private String filePath = null;
    public static final int MEDIA_TYPE_IMAGE = 1;
    FloatingActionButton fabCamera;
    String firstName, lastName, mobileNo;
    //  ScrollView svMain;
    boolean isImageUpdate = false;
    CoordinatorLayout mainLayout;
    Toolbar toolBar;
    private ArrayList<String> ndList;
    private ArrayList<String> rdsList;
    private String selectedND = "";
    private boolean isNDChange = true;
    private String userRole;
    ArrayList<String> stateList;
    ArrayList<String> cityList;
    ArrayList<String> zonelist;
    private String selectedZone = "";
    private boolean isZoneChange = true;
    private String selectedState = "";
    private boolean isStateChange = true;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.edit_profile_activity);
        toolBar = (Toolbar) findViewById(R.id.tool_bar);
        toolBar.setTitle("Edit Profile");
        setSupportActionBar(toolBar);

        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);

        toolBar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent();
                intent.putExtra("clean", false);
                setResult(101, intent);
                hideKeypad();
                finish();
            }
        });

        initView();
        getUserData();
    }


    private void initView() {
        mainLayout = (CoordinatorLayout) findViewById(R.id.main);
        etFirstName = (EditText) findViewById(R.id.etFirstName);
        etLastName = (EditText) findViewById(R.id.etLastName);
        etContact = (EditText) findViewById(R.id.etContact);
        etEmail = (EditText) findViewById(R.id.etEmail);
        etND = (TextView) findViewById(R.id.etND);
        etND.setOnClickListener(this);
        etRDS = (TextView) findViewById(R.id.etRDS);
        etRDS.setOnClickListener(this);
        etZone = (TextView) findViewById(R.id.etZone);
        etZone.setOnClickListener(this);
        etCity = (TextView) findViewById(R.id.etCity);
        etCity.setOnClickListener(this);
        etState = (TextView) findViewById(R.id.etState);
        etState.setOnClickListener(this);
        ivSave = (ImageView) findViewById(R.id.ivSave);
        ivSave.setOnClickListener(this);
        ivProfilePic = (ImageView) findViewById(R.id.ivProfilePic);
        ivProfilePic.setOnClickListener(this);

        fabCamera = (FloatingActionButton) findViewById(R.id.fabCamera);
        fabCamera.setOnClickListener(this);

        dbHandler = DataBaseHandler.getInstance(EditProfileScreen.this);

        user = dbHandler.getUser();

        imageLoader = ImageLoader.getInstance();
        ImageLoaderConfiguration config = new ImageLoaderConfiguration.Builder(EditProfileScreen.this)
                .memoryCacheSize(41943040)
                .discCacheSize(104857600)
                .threadPoolSize(10)
                .build();
        imageLoader.init(config);
        options = new DisplayImageOptions.Builder()
                .cacheInMemory(true)
                .cacheOnDisk(true)
                .showStubImage(R.drawable.phone)
                .showImageOnFail(R.drawable.phone)
                .considerExifParams(true)
                .displayer(new RoundedBitmapDisplayer(150))
                .build();


    }

    private void getUserData() {
        networkTask = new NetworkTask(EditProfileScreen.this, GET_USER_INFO, null, null);
        networkTask.exposePostExecute(EditProfileScreen.this);
        networkTask.execute(NetworkConstants.USER_DATA + user.getUserId() + "?access_token=" + DataStore.getAuthToken(this, DataStore.AUTH_TOKEN));


    }


    private void setUserData() {

        List<NameValuePair> params = new ArrayList<NameValuePair>();
        //  params.add(new BasicNameValuePair("image", filePath));
        params.add(new BasicNameValuePair("first_name", etFirstName.getText().toString()));
        params.add(new BasicNameValuePair("last_name", etLastName.getText().toString()));
        params.add(new BasicNameValuePair("contact", etContact.getText().toString()));
        params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN)));
        params.add(new BasicNameValuePair("email", etEmail.getText().toString()));
        params.add(new BasicNameValuePair("zone", etZone.getText().toString()));
        params.add(new BasicNameValuePair("nd_name", etND.getText().toString()));
        params.add(new BasicNameValuePair("rd_name", etRDS.getText().toString()));
        params.add(new BasicNameValuePair("city", etCity.getText().toString()));
        params.add(new BasicNameValuePair("state", etState.getText().toString()));
        if (filePath != null) {
            File file = new File(filePath);
            networkTask = new NetworkTask(EditProfileScreen.this, SET_USER_INFO, true, params, file);
        } else {
            networkTask = new NetworkTask(EditProfileScreen.this, SET_USER_INFO, params);
        }
        networkTask.exposePostExecute(EditProfileScreen.this);
        networkTask.execute(NetworkConstants.SET_USER_DATA + "/" + user.getUserId());


    }


    @Override
    public void resultFromNetwork(String object, int id, Object arg1, Object arg2) {
        if (object != null && !object.equals("")) {

            try {
                JSONObject main = new JSONObject(object);

                if (id == GET_USER_INFO) {
                    if (main.has("status") && main.optString("status").equals("success")) {
                        JSONObject data = main.optJSONObject("data");
                        firstName = data.optString("first_name");
                        etFirstName.setText(firstName);
                        etFirstName.setSelection(etFirstName.getText().length());
                        lastName = data.optString("last_name");
                        etLastName.setText(data.optString("last_name"));
                        mobileNo = data.optString("contact");
                        etContact.setText(data.optString("contact"));
                        etEmail.setText(data.optString("email"));
                        selectedND = data.optString("nd_name");
                        etND.setText(selectedND);
                        etRDS.setText(data.optString("rd_name"));
                        selectedZone = data.optString("zone");
                        etZone.setText(selectedZone);
                        etCity.setText(data.optString("city"));
                        selectedState = data.optString("state");
                        etState.setText(selectedState);

                        userRole = data.optString("role");

//                        String imageUrl = "file:///" + user.getUserImageLocalUrl();
//                        imageLoader.displayImage(imageUrl, ivProfilePic, options, null);

                        if (user.getUserImageLocalUrl() == null || user.getUserImageLocalUrl().equals("") || !(new File(user.getUserImageLocalUrl())).exists()) {
                            imageLoader.displayImage(NetworkConstants.BASE_URL + user.getUserImageServerUrl() + "/" + user.getUserImage(), ivProfilePic, options, null);
                        } else {
                            String imageUrl = "file:///" + user.getUserImageLocalUrl();
                            imageLoader.displayImage(imageUrl, ivProfilePic, options, null);

                        }

                    } else {
                        Util.createSnackBar(mainLayout, getString(R.string.msg_no_response));
                    }

                } else if (id == SET_USER_INFO) {

                    if (main.has("error")) {
                        if (main.getString("error").equals("access_denied")) {
                            Util.createToast(getApplicationContext(), getString(R.string.session_time_out_msg));
                            dbHandler.deleteUser();
                            startActivity(new Intent(EditProfileScreen.this, LoginActivity.class));
                            finish();
                        } else
                            Util.createSnackBar(mainLayout, getString(R.string.msg_no_response));
                    } else {
                        if (main.has("status") && main.getString("status").equals("success")) {
                            JSONObject object1 = new JSONObject(String.valueOf(main.optJSONObject("result")));
                            if (object1.has("msg") && object1.optString("msg").toString().length() > 0) {

                                Util.createToast(this, object1.optString("msg"));
                                Intent intent = new Intent(this, MainActivity.class);
                                startActivity(intent);
                                finish();
                            } else
                                Util.createSnackBar(mainLayout, "No changes detected");

                            if (userRole != null && !userRole.isEmpty()) {
                                if (userRole.equalsIgnoreCase(TRAINER_ROLE) || userRole.equalsIgnoreCase(ADMIN_ROLE) || userRole.equalsIgnoreCase(SUPER_ADMIN_ROLE)) {
                                    dbHandler.updateUserName(user.getUserId(), etFirstName.getText().toString() + " " + etLastName.getText().toString(), etEmail.getText().toString().trim());
                                    if (filePath != null)
                                        dbHandler.addUserProfileImage(user.getUserId(), filePath);
                                }
                            }
                            MainActivity.adapter.notifyDataSetChanged();
                        }
                    }
                } else if (id == GET_ND_LIST) {
                    if (main.has("status") && main.getString("status").equals("success")) {
                        Gson gson = new Gson();
                        NDListBean listBeanResponse = gson.fromJson(object, NDListBean.class);
                        if (listBeanResponse.getData() != null && listBeanResponse.getData().size() > 0) {
                            ndList = new ArrayList<>();
                            ndList.addAll(listBeanResponse.getData());
                            showDialogForCAtegory(ndList, 1);
                        }
                    }
                } else if (id == GET_RDS_LIST) {
                    if (main.has("status") && main.getString("status").equals("success")) {
                        Gson gson = new Gson();
                        NDListBean listBeanResponse = gson.fromJson(object, NDListBean.class);
                        if (listBeanResponse.getData() != null && listBeanResponse.getData().size() > 0) {
                            rdsList = new ArrayList<>();
                            rdsList.addAll(listBeanResponse.getData());
                            showDialogForCAtegory(rdsList, 2);
                        }
                    }

                } else if (id == GET_ZONE_LIST) {

                    if (main.has("status") && main.optString("status").equals("success")) {
                        JSONArray data = main.optJSONArray("data");
                        zonelist = new ArrayList<>();
                        String zone = "";
                        if (data != null && data.length() > 0) {
                            for (int i = 0; i < data.length(); i++) {
                                JSONObject obj = data.getJSONObject(i);
                                zone = obj.optString("zone_name");
                                zonelist.add(zone);
                            }
                            showDialogForCAtegory(zonelist, 3);
                        } else {
                            Util.createToast(EditProfileScreen.this, getString(R.string.msg_no_zone));
                        }
                    } else
                        Util.createToast(EditProfileScreen.this, getString(R.string.msg_no_zone));

                } else if (id == GET_STATE) {
                    if (main.has("status") && main.optString("status").equals("success")) {
                        JSONArray data = main.optJSONArray("data");
                        stateList = new ArrayList<>();
                        String state = "";
                        if (data != null && data.length() > 0) {
                            for (int i = 0; i < data.length(); i++) {
                                JSONObject obj = data.getJSONObject(i);
                                state = obj.optString("state_name");
                                stateList.add(state);
                            }
                            showDialogForCAtegory(stateList, 4);
                        } else {
                            Util.createToast(EditProfileScreen.this, getString(R.string.msg_no_state_found));
                        }
                    } else
                        Util.createToast(EditProfileScreen.this, getString(R.string.msg_no_state_found));


                } else if (id == GET_CITY) {
                    if (main.has("status") && main.optString("status").equals("success")) {
                        JSONArray data = main.optJSONArray("data");
                        cityList = new ArrayList<>();
                        String city = "";
                        if (data != null && data.length() > 0) {
                            for (int i = 0; i < data.length(); i++) {
                                JSONObject obj = data.getJSONObject(i);
                                city = obj.optString("city_name");
                                cityList.add(city);
                            }
                            showDialogForCAtegory(cityList, 5);
                        } else {
                            Util.createToast(EditProfileScreen.this, getString(R.string.msg_no_city_found));
                        }
                    } else
                        Util.createToast(EditProfileScreen.this, getString(R.string.msg_no_city_found));
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }
    }

    private boolean validateChangeInProfile() {
        if (!firstName.equals(etFirstName.getText().toString().trim()))
            return true;
        if (!lastName.equals(etLastName.getText().toString().trim()))
            return true;
        if (!mobileNo.equals(etContact.getText().toString().trim()))
            return true;
        if (!mobileNo.equals(etContact.getText().toString().trim()))
            return true;
        if (isImageUpdate)
            return true;

        return false;
    }

    @Override
    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.ivSave:
                if (Util.isNetworkAvailable(EditProfileScreen.this)) {
                    if (checkValidation()) {
                        hideKeypad();
                        setUserData();
                    }
                } else {
                    Util.createSnackBar(mainLayout, getString(R.string.msg_no_internet));
                }

                break;
            case R.id.fabCamera:
                isImageUpdate = true;
                imagePickerDialog();

                break;
            case R.id.etND: {
                getNDListfromServer();
                break;
            }
            case R.id.etRDS: {
                if (etND.getText().toString().isEmpty()) {
                    hideKeypad();
                    Util.createSnackBar(mainLayout, getString(R.string.warning_nd));
                } else
                    getRDSListfromServer();
                break;
            }
            case R.id.etZone: {
                getZoneListfromServer();
                break;
            }
            case R.id.etState: {
                if (etZone.getText().toString().isEmpty()) {
                    hideKeypad();
                    Util.createSnackBar(mainLayout, getString(R.string.warning_zone));
                } else
                    getStateList();

                break;
            }
            case R.id.etCity: {
                if (etState.getText().toString().isEmpty()) {
                    hideKeypad();
                    Util.createSnackBar(mainLayout, getString(R.string.warning_state));
                } else
                    getCityList();

                break;
            }
        }
    }

    private void getNDListfromServer() {
        if (ndList != null && ndList.size() > 0) {
            showDialogForCAtegory(ndList, 1);
        } else {
            networkTask = new NetworkTask(EditProfileScreen.this, GET_ND_LIST, null, null);
            networkTask.exposePostExecute(EditProfileScreen.this);
            networkTask.execute(NetworkConstants.GETNDLIST + "?access_token=" + DataStore.getAuthToken(this, DataStore.AUTH_TOKEN));
        }
    }

    private void getRDSListfromServer() {

        if (rdsList != null && rdsList.size() > 0 && isNDChange) {
            showDialogForCAtegory(rdsList, 2);
        } else {
            if (!selectedND.equals("")) {
                isNDChange = false;
                networkTask = new NetworkTask(EditProfileScreen.this, GET_RDS_LIST, null, null);
                networkTask.exposePostExecute(EditProfileScreen.this);
                String nd = "";
                try {
                    nd = URLEncoder.encode(selectedND, "UTF-8");
                } catch (UnsupportedEncodingException ex) {

                }
                networkTask.execute(NetworkConstants.GETRDSLIST + "?access_token=" + DataStore.getAuthToken(this, DataStore.AUTH_TOKEN) + "&nd_name=" + nd);
            } else {

            }

        }


    }

    private void getZoneListfromServer() {
        if (zonelist != null && zonelist.size() > 0) {
            showDialogForCAtegory(zonelist, 3);
        } else {
            List<NameValuePair> params = new ArrayList<NameValuePair>();
            params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN)));
            networkTask = new NetworkTask(EditProfileScreen.this, GET_ZONE_LIST, params, null);
            networkTask.exposePostExecute(EditProfileScreen.this);
            networkTask.execute(NetworkConstants.GETZONELIST);
        }
    }

    private void getStateList() {

        if (stateList != null && stateList.size() > 0 && isZoneChange) {
            showDialogForCAtegory(stateList, 4);
        } else {
            if (!selectedZone.equals("")) {
                isZoneChange = false;
                List<NameValuePair> params = new ArrayList<NameValuePair>();
                params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN)));
                params.add(new BasicNameValuePair("zone_name", selectedZone));
                networkTask = new NetworkTask(EditProfileScreen.this, GET_STATE, params, null);
                networkTask.exposePostExecute(EditProfileScreen.this);
                networkTask.execute(NetworkConstants.GET_STATE_LIST);
            }

        }

    }


    private void getCityList() {

        if (cityList != null && cityList.size() > 0 && isStateChange == false) {
            showDialogForCAtegory(cityList, 5);
        } else {
            if (!selectedState.equals("")) {
                isStateChange = false;
                List<NameValuePair> params = new ArrayList<NameValuePair>();
                params.add(new BasicNameValuePair("access_token", DataStore.getAuthToken(this, DataStore.AUTH_TOKEN)));
                params.add(new BasicNameValuePair("state_name", selectedState));
                networkTask = new NetworkTask(EditProfileScreen.this, GET_CITY, params, null);
                networkTask.exposePostExecute(EditProfileScreen.this);
                networkTask.execute(NetworkConstants.GET_CITY_LIST);
            }
        }

    }


    private boolean checkValidation() {
        if (etFirstName.getText().toString().equals("")) {
            hideKeypad();
            Util.createSnackBar(mainLayout, getString(R.string.msg_no_first_name));
            return false;
        } else if (etLastName.getText().toString().equals("")) {
            hideKeypad();
            Util.createSnackBar(mainLayout, getString(R.string.msg_no_last_name));
            return false;
        } else if (etContact.getText().toString().equals("")) {
            hideKeypad();
            Util.createSnackBar(mainLayout, getString(R.string.msg_no_contact_no));
            return false;
        } else if (etEmail.getText().toString().isEmpty()) {
            hideKeypad();
            Util.createSnackBar(mainLayout, getString(R.string.msg_no_email));
            return false;
        }/*else if(etND.getText().toString().isEmpty()){
            hideKeypad();
            Util.createSnackBar(mainLayout, getString(R.string.msg_no_nd));
            return false;
        }else if(etRDS.getText().toString().isEmpty()){
            hideKeypad();
            Util.createSnackBar(mainLayout, getString(R.string.msg_no_rds));
            return false;
        }else if(etState.getText().toString().isEmpty()){
            hideKeypad();
            Util.createSnackBar(mainLayout, getString(R.string.msg_no_state));
            return false;
        }else if(etCity.getText().toString().isEmpty()){
            hideKeypad();
            Util.createSnackBar(mainLayout, getString(R.string.msg_no_city));
            return false;
        } */ else
            return true;

    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == RESULT_LOAD) {
            if (resultCode == RESULT_OK) {
                try {
                    Uri selectedImage = data.getData();
                    String[] filePathColumn = {
                            MediaStore.Images.Media.DATA};
                    // Get the cursor
                    Cursor cursor = getContentResolver().query(selectedImage,
                            filePathColumn, null, null, null);
                    // Move to first row
                    cursor.moveToFirst();

                    int columnIndex = cursor.getColumnIndex(filePathColumn[0]);
                    filePath = cursor.getString(columnIndex);
                    cursor.close();

                    // Set the Image in ImageView after decoding the String
                    //    ivProfilePic.setImageBitmap(BitmapFactory
                    //           .decodeFile(filePath));

                    String imagePath = "file:///" + filePath;
                    imageLoader.displayImage(imagePath, ivProfilePic, options, null);
                } catch (Exception e) {
                }
            }
        } else if (requestCode == CAMERA_CAPTURE_IMAGE_REQUEST_CODE) {
            if (resultCode == RESULT_OK) {

                // successfully captured the image
                // launching upload activity
                try {
                    previewMedia(fileUri.getPath());
                } catch (Exception e) {
                }

            }
        }
    }

    private void imagePickerDialog() {
        final Dialog dialog = new Dialog(EditProfileScreen.this);
        dialog.setCancelable(true);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.choose_image_dialog);
        dialog.getWindow().setLayout(LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
        TextView tvHeader = (TextView) dialog.findViewById(R.id.tvHeader);
        tvHeader.setTypeface(Util.getRoboMedium(this));
        TextView tvCamera = (TextView) dialog.findViewById(R.id.tvCamera);
        tvCamera.setTypeface(Util.getRoboRegular(this));
        TextView tvGallery = (TextView) dialog.findViewById(R.id.tvGallery);
        tvGallery.setTypeface(Util.getRoboRegular(this));

        tvCamera.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                try {
                    Intent intent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);

                    fileUri = getOutputMediaFileUri(MEDIA_TYPE_IMAGE);

                    intent.putExtra(MediaStore.EXTRA_OUTPUT, fileUri);
                    startActivityForResult(intent, CAMERA_CAPTURE_IMAGE_REQUEST_CODE);
                } catch (Exception e) {
                    e.printStackTrace();
                }
                dialog.dismiss();
            }
        });

        tvGallery.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                try {
                    Intent galleryIntent = new Intent(Intent.ACTION_PICK,
                            MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
                    // Start the Intent
                    startActivityForResult(galleryIntent, RESULT_LOAD);
                } catch (Exception e) {
                    e.printStackTrace();
                }
                dialog.dismiss();
            }
        });

        dialog.show();
    }

    public Uri getOutputMediaFileUri(int type) {
        return Uri.fromFile(getOutputMediaFile(type));
    }

    private static File getOutputMediaFile(int type) {

        // External sdcard location
        File mediaStorageDir = new File(
                Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_PICTURES),
                NetworkConstants.hideFolderFromGallery + "GioneeStar");

        // Create the storage directory if it does not exist
        if (!mediaStorageDir.exists()) {
            if (!mediaStorageDir.mkdirs()) {

                return null;
            }
        }

        // Create a media file name
        String timeStamp = new SimpleDateFormat("yyyyMMdd_HHmmss",
                Locale.getDefault()).format(new Date());
        File mediaFile;
        if (type == MEDIA_TYPE_IMAGE) {
            mediaFile = new File(mediaStorageDir.getPath() + File.separator
                    + "IMG_" + timeStamp + ".jpg");
        } else {
            return null;
        }

        return mediaFile;
    }

    private void previewMedia(String filePath) {
        // Checking whether captured media is image or video
        this.filePath = filePath;


        // bimatp factory
        //  BitmapFactory.Options options = new BitmapFactory.Options();

        // down sizing image as it throws OutOfMemory Exception for larger
        // images
        // options.inSampleSize = 8;

        //    final Bitmap bitmap = BitmapFactory.decodeFile(filePath, options);

        //    ivProfilePic.setImageBitmap(bitmap);
        String imagePath = "file:///" + filePath;

        imageLoader.displayImage(imagePath, ivProfilePic, options, null);

    }

    private void hideKeypad() {
        if (getCurrentFocus() != null) {
            InputMethodManager inputMethodManager = (InputMethodManager) getSystemService(INPUT_METHOD_SERVICE);
            inputMethodManager.hideSoftInputFromWindow(getCurrentFocus().getWindowToken(), 0);
        }
    }


    public void showDialogForCAtegory(final ArrayList<String> placeResposnseDataList,
                                      final int type) {

        try {

            final ArrayList<String> tempCategory = new ArrayList<>();
            tempCategory.addAll(placeResposnseDataList);


            final ListView listView;
            final Dialog dialog = new Dialog(this, R.style.ThemeDialogCustom);
            dialog.setContentView(R.layout.select_dropdown);
            dialog.getWindow().setLayout(LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
            dialog.getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_ADJUST_RESIZE);
            dialog.setCanceledOnTouchOutside(true);
            final EditText et_name = (EditText) dialog.findViewById(R.id.tv_Name);
            if (type == 1)
                et_name.setHint("Search ND");
            else if (type == 2)
                et_name.setHint("Search RDS");
            else if (type == 3)
                et_name.setHint("Search Zone");
            else if (type == 4)
                et_name.setHint("Search State");
            else if (type ==5)
                et_name.setHint("Search City");
            final TextView iconSearch = (TextView) dialog.findViewById(R.id.search_icon);
            listView = (ListView) dialog.findViewById(android.R.id.list);

            final CountryAdeptor adeptor = new CountryAdeptor(tempCategory, this);
            adeptor.notifyDataSetChanged();
            listView.setAdapter(adeptor);
            et_name.addTextChangedListener(new TextWatcher() {

                @Override
                public void onTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
                    tempCategory.clear();
                    if (arg0.length() > 0) {
                        iconSearch.setVisibility(View.GONE);
                    } else {
                        iconSearch.setVisibility(View.VISIBLE);

                    }

                    if (!arg0.toString().equalsIgnoreCase("")) {
                        String searchString = et_name.getText().toString();
                        tempCategory.clear();
                        for (int i = 0; i < placeResposnseDataList.size(); i++) {
                            if (placeResposnseDataList.get(i).toLowerCase().startsWith(
                                    searchString.toLowerCase())) {

                                tempCategory.add(placeResposnseDataList.get(i));

                            }
                        }
                        adeptor.notifyDataSetChanged();


                    } else {
                        for (int i = 0; i < placeResposnseDataList.size(); i++)
                            tempCategory.add(placeResposnseDataList.get(i));

                        adeptor.notifyDataSetChanged();
                    }


                }

                @Override
                public void beforeTextChanged(CharSequence arg0,
                                              int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable arg0) {

                }

            });
            listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {

                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int pos, long arg3) {
                    try {
                        if (tempCategory != null && tempCategory.size() > 0) {
                            switch (type) {
                                case 1: {
                                    String name = tempCategory.get(pos);
                                    etND.setText(name);
                                    if (selectedND.equals("") || !selectedND.equals(name)) {
                                        selectedND = tempCategory.get(pos);
                                        isNDChange = true;
                                        etRDS.setText("");
                                    } else if (selectedND.equals(name)) {
                                        isNDChange = false;
                                    }
                                    selectedND = tempCategory.get(pos);
                                    break;
                                }
                                case 2: {
                                    String name = tempCategory.get(pos);
                                    etRDS.setText(name);
                                    break;
                                }
                                case 3: {
                                    String name = tempCategory.get(pos);
                                    etZone.setText(name);
                                    if (selectedZone.equals("") || !selectedZone.equals(name)) {
                                        selectedZone = tempCategory.get(pos);
                                        isZoneChange = true;
                                        isStateChange = true;
                                        etState.setText("");
                                        etCity.setText("");
                                    } else if (selectedZone.equals(name)) {
                                        isZoneChange = false;
                                        isStateChange = false;
                                    }
                                    selectedZone = tempCategory.get(pos);

                                    break;
                                }
                                case 4: {

                                    String name = tempCategory.get(pos);
                                    etState.setText(name);
                                    if (selectedState.equals("") || !selectedState.equals(name)) {
                                        selectedState = tempCategory.get(pos);

                                        isStateChange = true;

                                        etCity.setText("");
                                    } else if (selectedState.equals(name)) {

                                        isStateChange = false;
                                    }
                                    selectedState = tempCategory.get(pos);

                                    break;
                                }
                                case 5: {
                                    etCity.setText(tempCategory.get(pos).toString());
                                    break;
                                }
                            }

                            if (dialog != null)
                                dialog.dismiss();
                        }

                    } catch (Exception e) {
                        if (dialog != null)
                            dialog.dismiss();
                    }

                }

            });

            dialog.show();


        } catch (Exception e) {
            e.printStackTrace();
        }
    }


}
