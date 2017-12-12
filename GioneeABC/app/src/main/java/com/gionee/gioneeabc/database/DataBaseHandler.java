package com.gionee.gioneeabc.database;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import com.gionee.gioneeabc.bean.CategoryBean;
import com.gionee.gioneeabc.bean.DocumentBean;
import com.gionee.gioneeabc.bean.ImageBean;
import com.gionee.gioneeabc.bean.ProductBean;
import com.gionee.gioneeabc.bean.UserBean;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by Linchpin25 on 1/28/2016.
 */
public class DataBaseHandler extends SQLiteOpenHelper {

    private static final String DATABASE_NAME = "GioneeABC";
    private static final int DATABASE_VERSION = 4;
    public static DataBaseHandler dbHandler = null;


    // USER TABLE NAME
    private static final String USER_TABLE = "user";
    // USER TABLE COLUMNS
    private static final String USER_ID = "user_id";
    private static final String USER_NAME = "user_name";
    private static final String USER_EMAIL = "user_email";
    private static final String USER_IMG_NAME = "user_img_name";
    //private static final String USER_STATUS ="status";
    private static final String USER_IMG_SERVER_PATH = "user_server_path";
    private static final String USER_IMG_LOCAL_PATH = "user_img_local_path";
    // CREATING USER TABLE
    private static final String CREATE_USER_TABLE = "CREATE TABLE " + USER_TABLE + "(" + USER_ID + " INTEGER PRIMARY KEY, " + USER_NAME + " TEXT, "
            + USER_EMAIL + " TEXT, " + USER_IMG_NAME + " TEXT, " + USER_IMG_SERVER_PATH + " TEXT, " + USER_IMG_LOCAL_PATH + " TEXT " + ")";


    //APP DATA TABLE
    private static final String APP_DATA_TABLE = "app_data";
    // APP DATA TABLE COLUMNS
    private static final String LAST_UPDATE = "last_update";
    // CREATING APP DATA TABLE
    private static final String CREATE_APP_DATA_TABLE = "CREATE TABLE " + APP_DATA_TABLE + "(" + LAST_UPDATE + " TEXT" + ")";


    // CATEGORY TABLE NAME
    private static final String CATEGORY_TABLE = "categories";
    // CATEGORY TABLE COLUMNS
    public static final String CATEGORY_ID = "cat_id";
    private static final String CATEGORY_NAME = "cat_name";
    private static final String CATEGORY_PARENT_ID = "cat_parent_id";
    private static final String CATEGORY_DESC = "cat_desc";
    private static final String CATEGORY_IMAGE_NAME = "cat_image_name";
    private static final String CATEGORY_IMAGE_ID = "cat_image_id";
    private static final String CATEGORY_IMAGE_SERVER_PATH = "cat_image_server";
    private static final String CATEGORY_IMAGE_LOCAL_PATH = "cat_image_local";
    private static final String CATEGORY_POSITION = "cat_position";
    // CREATING CATEGORY TABLE
    String CREATE_CATEGORY_TABLE = "CREATE TABLE " + CATEGORY_TABLE + "(" + CATEGORY_ID + " INTEGER PRIMARY KEY, " + CATEGORY_NAME +
            " TEXT, " + CATEGORY_PARENT_ID + " INTEGER, " + CATEGORY_DESC + " TEXT, " + CATEGORY_IMAGE_NAME + " TEXT,"
            + CATEGORY_IMAGE_ID + " INTEGER, " + CATEGORY_IMAGE_SERVER_PATH + " TEXT, " + CATEGORY_IMAGE_LOCAL_PATH + " TEXT, "
            + CATEGORY_POSITION + " INTEGER " + ")";
/*
    String CREATE_IMAGES_TABLE = "CREATE TABLE " + IMAGE_TABLE + "(" + IMAGE_ID + " INTEGER PRIMARY KEY, " + IMAGE_NAME +
            " TEXT, " + IMAGE_TYPE + " TEXT, " + IMAGE_LOCAL_PATH + " TEXT, " + IMAGE_SERVER_PATH + " TEXT, " + MODEL_ID +
            " INTEGER " + ")";
*/

    // PRODUCTS TABLE NAME
    private static final String PRODUCTS_TABLE = "products";
    private static final String NEW_PRODUCTS_TABLE = "new_products";
    // PRODUCTS TABLE COLUMNS
    private static final String PRODUCT_ID = "product_id";
    private static final String PRODUCT_CAT_ID = "product_cat_id";
    private static final String PRODUCT_NAME = "product_name";
    private static final String IS_NEW_PRODUCT = "is_new_product";
    private static final String PRODUCT_DESC = "product_desc";
    private static final String PRODUCT_DESC1 = "product_desc_1";
    private static final String PRODUCT_DESC2 = "product_desc_2";
    private static final String PRODUCT_IMAGE = "product_image";
    private static final String PRODUCT_VAULT = "product_vault";
    private static final String PRODUCT_IMAGE_SERVER_PATH = "product_image_server";
    private static final String PRODUCT_IMAGE_LOCAL_PATH = "product_image_local";
    private static final String PRODUCT_IMAGE_ID = "product_image_id";
    private static final String PRODUCT_IMAGES_JSON = "product_images_json";
    private static final String LAUNCH_DATE = "launch_date";

    // CREATING PRODUCTS TABLE
    String CREATE_PRODUCTS_TABLE = "CREATE TABLE " + PRODUCTS_TABLE + "(" + PRODUCT_ID + " INTEGER PRIMARY KEY, " + PRODUCT_NAME +
            " TEXT, " + PRODUCT_VAULT + " TEXT, " + PRODUCT_DESC + " TEXT, " + PRODUCT_DESC1 + " TEXT, " + IS_NEW_PRODUCT + " TEXT, " + PRODUCT_DESC2 + " TEXT," +
            PRODUCT_CAT_ID + " INTEGER, " + PRODUCT_IMAGE + " TEXT, " + PRODUCT_IMAGE_SERVER_PATH + " TEXT, " +
            PRODUCT_IMAGE_LOCAL_PATH + " TEXT, " + PRODUCT_IMAGES_JSON + " TEXT, " + PRODUCT_IMAGE_ID + " INTEGER, " + LAUNCH_DATE + " TEXT, " + " FOREIGN KEY (" + PRODUCT_CAT_ID +
            ") REFERENCES " + CATEGORY_TABLE + " (" + CATEGORY_ID + ")" + ")";


    String CREATE_NEW_PRODUCTS_TABLE = "CREATE TABLE " + NEW_PRODUCTS_TABLE + "(" + PRODUCT_ID + " INTEGER PRIMARY KEY, " + PRODUCT_NAME +
            " TEXT, " + PRODUCT_VAULT + " TEXT, " + PRODUCT_DESC + " TEXT, " + PRODUCT_DESC1 + " TEXT, " + IS_NEW_PRODUCT + " TEXT, " + PRODUCT_DESC2 + " TEXT," +
            PRODUCT_CAT_ID + " INTEGER, " + PRODUCT_IMAGE + " TEXT, " + PRODUCT_IMAGE_SERVER_PATH + " TEXT, " +
            PRODUCT_IMAGE_LOCAL_PATH + " TEXT, " + PRODUCT_IMAGES_JSON + " TEXT, " + PRODUCT_IMAGE_ID + " INTEGER, " + LAUNCH_DATE + " TEXT, " + " FOREIGN KEY (" + PRODUCT_CAT_ID +
            ") REFERENCES " + CATEGORY_TABLE + " (" + CATEGORY_ID + ")" + ")";


    // IMAGES TABLE NAME
    private static final String IMAGE_TABLE = "images";
    // IMAGES TABLE COLUMNS
    private static final String IMAGE_ID = "image_id";
    private static final String IMAGE_NAME = "image_name";
    private static final String IMAGE_TYPE = "image_type";
    private static final String IMAGE_LOCAL_PATH = "image_local_path";
    private static final String IMAGE_SERVER_PATH = "image_server_path";
    private static final String MODEL_ID = "image_model_id";
    // CREATING IMAGE TABLE
    String CREATE_IMAGES_TABLE = "CREATE TABLE " + IMAGE_TABLE + "(" + IMAGE_ID + " INTEGER PRIMARY KEY, " + IMAGE_NAME +
            " TEXT, " + IMAGE_TYPE + " TEXT, " + IMAGE_LOCAL_PATH + " TEXT, " + IMAGE_SERVER_PATH + " TEXT, " + MODEL_ID +
            " INTEGER " + ")";


    private static final String IMAGE_TABLE_COLLECTION = "images_collection";
    // IMAGES TABLE COLUMNS
    private static final String IMAGE_ID_COLLECTION = "image_collection_id";
    private static final String IMAGE_NAME_COLLECTION = "image_collection_name";
    private static final String IMAGE_BYTE = "image_in_byte";


    //DOCUMENTS TABLE
    private static final String DOC_TABLE = "documents";
    // DOCUMENTS TABLE COLUMN
    private static final String DOC_ID = "document_id";
    private static final String DOC_TYPE = "document_type";
    private static final String DOC_NAME = "document_name";
    private static final String DOC_URL = "document_url";
    private static final String DOC_LOCAL_PATH = "document_local_path";
    private static final String DOC_PRODUCT_ID = "document_product_id";
    // CREATING DOCUMENT TABLE
    String CREATE_DOC_TABLE = "CREATE TABLE " + DOC_TABLE + "(" + DOC_ID + " INTEGER PRIMARY KEY, " + DOC_TYPE +
            " TEXT, " + DOC_NAME + " TEXT, " + DOC_URL + " TEXT, " + DOC_LOCAL_PATH + " TEXT, " +
            DOC_PRODUCT_ID + " INTEGER, " + " FOREIGN KEY (" + DOC_PRODUCT_ID +
            ") REFERENCES " + PRODUCTS_TABLE + " (" + PRODUCT_ID + ")" + ")";

    public static final String TABLE_GET_DATA = "table_get_data";
    public static final String COL_GET_DATA = "get_data";
    public static final String COL_DATA_TYPE = "data_type";

    public static final String TYPE_TUTORIAL_CATEGORY = "tutorial_category";
    public static final String TYPE_UPDATE_CATEGORY = "update_category";
    public static final String TYPE_RECOMM_MODEL = "recomm_model_data";
    public static final String TYPE_RECOMM_ATTRIB = "recomm_attrib_data";

    public static final String TABLE_SUBMIT_DATA = "table_submit_data";
    public static final String COL_SUBMIT_DATA = "submit_data";

    public static final String TYPE_LOGOUT = "logout";

    String CREATE_GET_TABLE = "CREATE TABLE " + TABLE_GET_DATA + "(" + COL_DATA_TYPE + " TEXT, " + COL_GET_DATA + " TEXT);";
    String CREATE_SUBMIT_TABLE = "CREATE TABLE " + TABLE_SUBMIT_DATA + "(" + COL_DATA_TYPE + " TEXT, " + COL_SUBMIT_DATA + " TEXT);";

    public static final String TABLE_MODULE_AUDIT_TRAIL = "table_module_audit_trail";
    public static final String COL_USER_ID = "user_id";
    public static final String COL_MODULE_NAME = "module_name";
    public static final String COL_ACCESS_TIME = "access_time";
    public static final String COL_LAST_LOGIN = "last_login";
    String CREATE_MODULE_AUDIT_TRAIL_TABLE = "CREATE TABLE " + TABLE_MODULE_AUDIT_TRAIL + "(" + COL_USER_ID + " INTEGER, " + COL_MODULE_NAME + " TEXT ," + COL_ACCESS_TIME + " INTEGER ," + COL_LAST_LOGIN + " TEXT );";

    public static final String TABLE_TOPIC_READ = "table_topic_read";
    public static final String SUB_CATEGORY_ID = "subcategory_id";
    public static final String TOPIC_ID = "topic_id";
    String CREATE_TABLE_TOPIC_READ = "CREATE TABLE " + TABLE_TOPIC_READ + "(" + COL_USER_ID + " INTEGER, " + CATEGORY_ID + " INTEGER ," + SUB_CATEGORY_ID + " INTEGER ," + TOPIC_ID + " INTEGER );";


    public DataBaseHandler(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    public static DataBaseHandler getInstance(Context context) {
        if (dbHandler == null) {
            synchronized (DataBaseHandler.class) {
                if (dbHandler == null) {
                    dbHandler = new DataBaseHandler(context);
                }
            }
        }
        return dbHandler;

    }


    @Override
    public void onCreate(SQLiteDatabase db) {
        db.execSQL(CREATE_APP_DATA_TABLE);
        db.execSQL(CREATE_USER_TABLE);
        db.execSQL(CREATE_CATEGORY_TABLE);
        db.execSQL(CREATE_PRODUCTS_TABLE);
        db.execSQL(CREATE_NEW_PRODUCTS_TABLE);
        db.execSQL(CREATE_IMAGES_TABLE);
        db.execSQL(CREATE_DOC_TABLE);
        db.execSQL(CREATE_GET_TABLE);
        db.execSQL(CREATE_SUBMIT_TABLE);
        db.execSQL(CREATE_MODULE_AUDIT_TRAIL_TABLE);
        db.execSQL(CREATE_TABLE_TOPIC_READ);
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
        db.execSQL("DROP TABLE IF EXISTS " + CATEGORY_TABLE);
        db.execSQL("DROP TABLE IF EXISTS " + PRODUCTS_TABLE);
        db.execSQL("DROP TABLE IF EXISTS " + USER_TABLE);
        db.execSQL("DROP TABLE IF EXISTS " + DOC_TABLE);
        db.execSQL("DROP TABLE IF EXISTS " + IMAGE_TABLE);
        db.execSQL("DROP TABLE IF EXISTS " + APP_DATA_TABLE);
        db.execSQL("DROP TABLE IF EXISTS " + NEW_PRODUCTS_TABLE);
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_GET_DATA);
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_SUBMIT_DATA);
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_MODULE_AUDIT_TRAIL);
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_TOPIC_READ);
        onCreate(db);
    }

    /*
    * ADDING IMAGE TO IMAGE TABLE
    * */
    public synchronized void addImage(ImageBean image) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(IMAGE_ID, image.getImageId());
        values.put(IMAGE_NAME, image.getImageName());
        values.put(IMAGE_TYPE, image.getImageType());
        values.put(IMAGE_LOCAL_PATH, image.getImageLocalPath());
        values.put(IMAGE_SERVER_PATH, image.getImageServerPath());
        values.put(MODEL_ID, image.getModelId());
        long i = db.insert(IMAGE_TABLE, null, values);
        if (i > -1)
            System.out.print("Successfully inserted image");
        else
            System.out.print("Fail to insert image");
        db.close();
    }

    public synchronized void addImageInCollection(ImageBean image) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(IMAGE_ID_COLLECTION, image.getImageId());
        values.put(IMAGE_NAME_COLLECTION, image.getImageName());
        values.put(IMAGE_BYTE, image.getImageByte());
        long i = db.insert(IMAGE_TABLE, null, values);
        if (i > -1)
            System.out.print("Successfully inserted image");
        else
            System.out.print("Fail to insert image");
        db.close();
    }

    /*
    * ADDING TIME TO APP DATA TABLE
    * */
    public synchronized void addTime(String lastUpdatedTime) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(LAST_UPDATE, lastUpdatedTime);

        long i = db.insert(APP_DATA_TABLE, null, values);
        if (i > -1)
            System.out.print("Successfully inserted updated time");
        else
            System.out.print("Fail to insert updated time");
    }

    /*
    * ADDING USER INTO DATABASE
    * */
    public synchronized void addUser(UserBean user) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(USER_ID, user.getUserId());
        values.put(USER_NAME, user.getUserName());
        values.put(USER_EMAIL, user.getUserEmail());
        //values.put(USER_STATUS,user.getStatus());
        values.put(USER_IMG_NAME, user.getUserImage());
        values.put(USER_IMG_SERVER_PATH, user.getUserImageServerUrl());
        values.put(USER_IMG_LOCAL_PATH, user.getUserImageLocalUrl());

        long i = db.insert(USER_TABLE, null, values);
        if (i > -1)
            System.out.print("Successfully inserted user");
        else
            System.out.print("Fail to insert user");
        db.close();
    }

    public synchronized void addCategory(CategoryBean category) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(CATEGORY_ID, category.getCategoryId());
        values.put(CATEGORY_NAME, category.getCategoryName()); // category name
        values.put(CATEGORY_PARENT_ID, category.getCategoryParentId());
        values.put(CATEGORY_DESC, category.getCategoryDesc());
        values.put(CATEGORY_IMAGE_NAME, category.getCategoryImage());
        values.put(CATEGORY_IMAGE_ID, category.getCategoryId());
        values.put(CATEGORY_IMAGE_SERVER_PATH, category.getImageServerPath());
        values.put(CATEGORY_IMAGE_LOCAL_PATH, category.getImageLocalPath());
        values.put(CATEGORY_POSITION, category.getCategoryPosition());
        long i;
        if (checkCategoryIsExist(category.getCategoryId()))
            i = db.update(CATEGORY_TABLE, values, CATEGORY_ID + " = " + category.getCategoryId(), null);
        else
            i = db.insert(CATEGORY_TABLE, null, values);
        if (i > -1)
            System.out.print("Successfully inserted category");
        else
            System.out.print("Fail to insert category");
        db.close(); // Closing database connection
    }

    public synchronized void addProduct(ProductBean product) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(PRODUCT_ID, product.getId());
        values.put(PRODUCT_CAT_ID, product.getCategoryId()); // product detail
        values.put(PRODUCT_NAME, product.getProductName());// product name
        values.put(PRODUCT_VAULT, product.getVaultDocsJson());
        values.put(PRODUCT_DESC, product.getProductDesc());
        values.put(PRODUCT_DESC1, product.getProductDesc1());
        values.put(PRODUCT_DESC2, product.getProductDesc2());
        values.put(PRODUCT_IMAGE, product.getProductImage());
        values.put(PRODUCT_IMAGE_SERVER_PATH, product.getProductImageServerPath());
        values.put(PRODUCT_IMAGE_LOCAL_PATH, product.getProductImageLocalPath());
        values.put(PRODUCT_IMAGES_JSON, product.getProductImagesJson());
        values.put(PRODUCT_IMAGE_ID, product.getImageId());
        values.put(IS_NEW_PRODUCT, product.getIsNewProduct());
        values.put(LAUNCH_DATE, product.getLaunch_date());
        // Inserting Row
        long i;
        if (checkProductIsExist(product.getId()))
            i = db.update(PRODUCTS_TABLE, values, PRODUCT_ID + " = " + product.getId(), null);
        else
            i = db.insert(PRODUCTS_TABLE, null, values);
        //updating Row
        if (i > -1)
            System.out.print("Successfully inserted product");
        else
            System.out.print("Fail to insert product");

        db.close(); // Closing database connection
    }



    public synchronized void addDocument(DocumentBean doc) {
        SQLiteDatabase db = this.getWritableDatabase();

        ContentValues values = new ContentValues();
        values.put(DOC_ID, doc.getDocId());
        values.put(DOC_NAME, doc.getDocName());
        values.put(DOC_TYPE, doc.getDocType());
        values.put(DOC_URL, doc.getDocUrl());
        values.put(DOC_LOCAL_PATH, doc.getDocLocalPath());
        values.put(DOC_PRODUCT_ID, doc.getProductId());

        // Inserting Row
        long i = db.insert(DOC_TABLE, null, values);
        if (i > -1)
            System.out.print("Successfully inserted document");
        else
            System.out.print("Fail to insert document");
        db.close(); // Closing database connection
    }

    public long addGetData(String data, String dataType) {
        SQLiteDatabase db = getWritableDatabase();
        ContentValues cv = new ContentValues();
        cv.put(COL_GET_DATA, data);
        cv.put(COL_DATA_TYPE, dataType);
        long l = db.insertWithOnConflict(TABLE_GET_DATA, null, cv, SQLiteDatabase.CONFLICT_REPLACE);
        db.close();
        return l;
    }

    public long addSubmitData(String data, String dataType) {
        SQLiteDatabase db = getWritableDatabase();
        ContentValues cv = new ContentValues();
        cv.put(COL_SUBMIT_DATA, data);
        cv.put(COL_DATA_TYPE, dataType);
        long l = db.insertWithOnConflict(TABLE_SUBMIT_DATA, null, cv, SQLiteDatabase.CONFLICT_REPLACE);
        db.close();
        return l;
    }

    public long addModuleAuditTrailData(int user_id, String moduleName, long time, String last_login) {
        SQLiteDatabase db = getWritableDatabase();
        ContentValues cv = new ContentValues();
        cv.put(COL_USER_ID, user_id);
        cv.put(COL_MODULE_NAME, moduleName);
        cv.put(COL_ACCESS_TIME, time);
        cv.put(COL_LAST_LOGIN, last_login);
        long l = db.insertWithOnConflict(TABLE_MODULE_AUDIT_TRAIL, null, cv, SQLiteDatabase.CONFLICT_REPLACE);
        db.close();
        return l;
    }

    public long addReadStatusData(int user_id, int cat_id, int sub_id, int topic_id) {
        SQLiteDatabase db = getWritableDatabase();
        ContentValues cv = new ContentValues();
        cv.put(COL_USER_ID, user_id);
        cv.put(CATEGORY_ID, cat_id);
        cv.put(SUB_CATEGORY_ID, sub_id);
        cv.put(TOPIC_ID, topic_id);
        long l = db.insertWithOnConflict(TABLE_TOPIC_READ, null, cv, SQLiteDatabase.CONFLICT_REPLACE);
        db.close();
        return l;
    }

    /*
    * UPDATE IMAGE LOCAL PATH
    * */
    public synchronized void addImageLocalPath(int id, String path) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(IMAGE_LOCAL_PATH, path);
        String whereArgs[] = {Integer.toString(id)};
        long i = db.update(IMAGE_TABLE, values, IMAGE_ID + "=?", whereArgs);
        if (i > -1)
            System.out.print("Successfully inserted image");
        else
            System.out.print("Fail to insert image");
        db.close();
    }

    /*
    * UPDATING USER PROFILE IMAGE INTO DATABASE
    * */
    public synchronized void addUserProfileImage(int id, String path) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(USER_IMG_LOCAL_PATH, path);

        String whereArgs[] = {Integer.toString(id)};
        long i = db.update(USER_TABLE, values, USER_ID + "= ?", whereArgs);

        if (i > -1)
            System.out.print("Successfully inserted user");
        else
            System.out.print("Fail to insert user");
        db.close();
    }

    public synchronized void addUserProfileImageServerUrl(int id, String image, String localPath) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
//        values.put(USER_IMG_SERVER_PATH, path);
        values.put(USER_IMG_NAME, image);
        values.put(USER_IMG_LOCAL_PATH, localPath);

        String whereArgs[] = {Integer.toString(id)};
        long i = db.update(USER_TABLE, values, USER_ID + "= ?", whereArgs);

        if (i > -1)
            System.out.print("Successfully inserted user");
        else
            System.out.print("Fail to insert user");
        db.close();
    }

    /*
    * UPDATE USER
    * */
    public synchronized void updateUserName(int id, String name, String email) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(USER_NAME, name);
        values.put(USER_EMAIL, email);

        String whereArgs[] = {Integer.toString(id)};
        long i = db.update(USER_TABLE, values, USER_ID + "= ?", whereArgs);
        if (i > -1)
            System.out.print("Successfully inserted user");
        else
            System.out.print("Fail to insert user");
        db.close();
    }

    public synchronized void updateCategoryLocalUrl(int id, String url) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(CATEGORY_IMAGE_LOCAL_PATH, url);

        String whereArgs[] = {Integer.toString(id)};
        long i = db.update(CATEGORY_TABLE, values, CATEGORY_ID + "= ?", whereArgs);

        if (i > -1)
            System.out.print("Successfully inserted category image local path");
        else
            System.out.print("Fail to insert category image local path");
        db.close();
    }

    public synchronized void updateDocumentLocalPath(int docId, String url) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(DOC_LOCAL_PATH, url);

        String whereArgs[] = {Integer.toString(docId)};
        long i = db.update(DOC_TABLE, values, DOC_ID + "= ?", whereArgs);
        if (i > -1)
            System.out.print("Successfully inserted category image local path");
        else
            System.out.print("Fail to insert category image local path");
        db.close();
    }

    public Cursor getAllTutorialCategory() {
        SQLiteDatabase db = getReadableDatabase();
        Cursor cursor = db.rawQuery("select * from " + TABLE_GET_DATA + " where " + COL_DATA_TYPE + " = '" + TYPE_TUTORIAL_CATEGORY + "'", null);
        return cursor;
    }

    public Cursor getAllUpdateCategory() {
        SQLiteDatabase db = getReadableDatabase();
        Cursor cursor = db.rawQuery("select * from " + TABLE_GET_DATA + " where " + COL_DATA_TYPE + " = '" + TYPE_UPDATE_CATEGORY + "'", null);
        return cursor;
    }

    public Cursor getAllRecommModelData() {
        SQLiteDatabase db = getReadableDatabase();
        Cursor cursor = db.rawQuery("select * from " + TABLE_GET_DATA + " where " + COL_DATA_TYPE + " = '" + TYPE_RECOMM_MODEL + "'", null);
        return cursor;
    }

    public Cursor getAllRecommAttribData() {
        SQLiteDatabase db = getReadableDatabase();
        Cursor cursor = db.rawQuery("select * from " + TABLE_GET_DATA + " where " + COL_DATA_TYPE + " = '" + TYPE_RECOMM_ATTRIB + "'", null);
        return cursor;
    }

    public Cursor getAllSubmitLogoutData() {
        SQLiteDatabase db = getReadableDatabase();
        Cursor cursor = db.rawQuery("select * from " + TABLE_SUBMIT_DATA + " where " + COL_DATA_TYPE + " = '" + TYPE_LOGOUT + "'", null);
        return cursor;
    }

    public Cursor getAllAuditTrailData(int user_id) {
        SQLiteDatabase db = getReadableDatabase();
        Cursor cursor = db.rawQuery("select * from " + TABLE_MODULE_AUDIT_TRAIL + " where " + COL_USER_ID + " = '" + user_id + "'", null);
        return cursor;
    }

    public Cursor getAllTopicReadData(int user_id) {
        SQLiteDatabase db = getReadableDatabase();
        Cursor cursor = db.rawQuery("select * from " + TABLE_TOPIC_READ + " where " + COL_USER_ID + " = '" + user_id + "'", null);
        return cursor;
    }

    /*
    * GETTING IMAGE'S FROM IMAG+E TABLE
    * */
    public List<ImageBean> getAllImages() {
        List<ImageBean> imageList = new ArrayList<ImageBean>();
        SQLiteDatabase db = this.getReadableDatabase();

        String GET_IMAGES = "SELECT * FROM " + IMAGE_TABLE;

        Cursor cursor = db.rawQuery(GET_IMAGES, null);
        if (cursor.moveToFirst()) {
            do {
                ImageBean image = new ImageBean(Integer.parseInt(cursor.getString(0)), cursor.getString(1), cursor.getString(2), cursor.getString(3), cursor.getString(4), Integer.parseInt(cursor.getString(5)));
                imageList.add(image);
            } while (cursor.moveToNext());
        }

        if (cursor != null)
            cursor.close();

        if (imageList.size() > 0)
            return imageList;
        else
            return null;
    }

    public List<ImageBean> getImageByModelId(int id) {
        List<ImageBean> imageList = new ArrayList<ImageBean>();

        String selectAllDocuments = "SELECT * FROM " + IMAGE_TABLE + " where " + MODEL_ID + "=" + id;

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectAllDocuments, null);
        if (cursor.moveToFirst()) {
            do {
                ImageBean image = new ImageBean(Integer.parseInt(cursor.getString(0)), cursor.getString(1), cursor.getString(2), cursor.getString(3), cursor.getString(4), Integer.parseInt(cursor.getString(5)));
                imageList.add(image);

                // Adding product to list
                imageList.add(image);
            } while (cursor.moveToNext());
        }
        if (cursor != null)
            cursor.close();
        if (imageList.size() > 0)
            return imageList;
        else
            return null;
    }

    public List<ImageBean> getDashBoardImages() {
        List<ImageBean> imageList = new ArrayList<ImageBean>();
        SQLiteDatabase db = this.getReadableDatabase();

        String GET_IMAGES = "SELECT * FROM " + IMAGE_TABLE + " WHERE " + IMAGE_TYPE + "='" + "DASHBOARD'";

        Cursor cursor = db.rawQuery(GET_IMAGES, null);
        if (cursor.moveToFirst()) {
            do {
                ImageBean image = new ImageBean(Integer.parseInt(cursor.getString(0)), cursor.getString(1), cursor.getString(2), cursor.getString(3), cursor.getString(4), Integer.parseInt(cursor.getString(5)));
                imageList.add(image);
            } while (cursor.moveToNext());
        }
        if (cursor != null)
            cursor.close();

        if (imageList.size() > 0)
            return imageList;
        else
            return null;
    }

    /*
    * PRODUCT THUMBNAIL
    * */
    public ImageBean getProductThumbnailImage(int productId) {
        List<ImageBean> imageList = new ArrayList<ImageBean>();
        SQLiteDatabase db = this.getReadableDatabase();

        Cursor cursor = db.rawQuery("SELECT * FROM " + IMAGE_TABLE + " WHERE " + IMAGE_TYPE + " = '" + "THUMBNAIL" + "'AND " + MODEL_ID + " = '" + productId + "'", null);

        if (cursor.moveToFirst()) {
            do {
                ImageBean image = new ImageBean(Integer.parseInt(cursor.getString(0)), cursor.getString(1), cursor.getString(2), cursor.getString(3), cursor.getString(4), Integer.parseInt(cursor.getString(5)));
                imageList.add(image);
            } while (cursor.moveToNext());
        }

        if (cursor != null)
            cursor.close();

        if (imageList.size() > 0)
            return imageList.get(0);
        else
            return null;
    }

    public String getLastUpdate() {
        String lastUpdatedTime = "";
        SQLiteDatabase db = this.getReadableDatabase();
        String GET_TIME = "SELECT * FROM " + APP_DATA_TABLE;
        Cursor cursor = db.rawQuery(GET_TIME, null);
        if (cursor.moveToFirst()) {
            do {
                lastUpdatedTime = cursor.getString(0);
            } while (cursor.moveToNext());
        }
        if (cursor != null)
            cursor.close();

        return lastUpdatedTime;
    }

    /*
    * GETTING USER FROM DATABASE
    * */
    public UserBean getUser() {
        List<UserBean> userList = new ArrayList<UserBean>();
        String selectUser = "SELECT * FROM " + USER_TABLE;
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(selectUser, null);
        if (cursor.moveToFirst()) {
            do {
                UserBean user = new UserBean();
                user.setUserId(Integer.parseInt(cursor.getString(0)));
                user.setUserName(cursor.getString(1));
                user.setUserEmail(cursor.getString(2));
                user.setUserImage(cursor.getString(3));
                user.setUserImageServerUrl(cursor.getString(4));
                user.setUserImageLocalUrl(cursor.getString(5));
                // Adding product to list
                userList.add(user);
            } while (cursor.moveToNext());
        }
        if (cursor != null)
            cursor.close();

        if (userList.size() > 0)
            return userList.get(0);
        else
            return null;
    }

    public boolean checkCategoryIsExist(int categoryID) {
        String queryCheck = "SELECT * FROM " + CATEGORY_TABLE + " WHERE " + CATEGORY_ID + " = " + categoryID;
        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(queryCheck, null);
        return cursor.getCount() == 1;
    }

    public CategoryBean getCategory(int id) {
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.query(CATEGORY_TABLE, new String[]{CATEGORY_ID, CATEGORY_NAME, CATEGORY_PARENT_ID, CATEGORY_DESC, CATEGORY_IMAGE_NAME, CATEGORY_IMAGE_ID, CATEGORY_IMAGE_SERVER_PATH, CATEGORY_IMAGE_LOCAL_PATH}, CATEGORY_ID + "=?" + new String[]{String.valueOf(id)}, null, null, null, null);
        if (cursor != null)
            cursor.moveToFirst();
        CategoryBean category = new CategoryBean(Integer.parseInt(cursor.getString(0)), cursor.getString(1), Integer.parseInt(cursor.getString(2)), cursor.getString(3), cursor.getString(4), Integer.parseInt(cursor.getString(5)), cursor.getString(6), cursor.getString(7), Integer.parseInt(cursor.getString(8)));

        if (cursor != null)
            cursor.close();
        return category;
    }

    public List<CategoryBean> getAllCategories() {
        List<CategoryBean> categoryList = new ArrayList<CategoryBean>();
        String selectNewProducts = "SELECT * FROM " + CATEGORY_TABLE + " WHERE " + CATEGORY_POSITION + " = 0  ";
        //String selectAllProducts = "SELECT * FROM " + CATEGORY_TABLE+ " ORDER BY " + CATEGORY_POSITION + " ASC, " + CATEGORY_ID + " DESC";

        SQLiteDatabase db1 = this.getReadableDatabase();
        Cursor cursor1 = db1.rawQuery(selectNewProducts, null);

        if (cursor1.moveToFirst()) {
            do {
                CategoryBean category = new CategoryBean();
                category.setCategoryId(Integer.parseInt(cursor1.getString(0)));
                category.setCategoryName(cursor1.getString(1));
                category.setCategoryParentId(Integer.parseInt(cursor1.getString(2)));
                category.setCategoryDesc(cursor1.getString(3));
                category.setCategoryImage(cursor1.getString(4));
                category.setImageId(Integer.parseInt(cursor1.getString(5)));
                category.setImageServerPath(cursor1.getString(6));
                category.setImageLocalPath(cursor1.getString(7));
                category.setCategoryPosition(Integer.parseInt(cursor1.getString(8)));
                // Adding product to list
                categoryList.add(category);
            } while (cursor1.moveToNext());
        }

        if (cursor1 != null)
            cursor1.close();

        String selectAllProducts = "SELECT * FROM " + CATEGORY_TABLE + " WHERE " + CATEGORY_ID + " > 0 ORDER BY " + CATEGORY_POSITION + " ASC ";
        //String selectAllProducts = "SELECT * FROM " + CATEGORY_TABLE+ " ORDER BY " + CATEGORY_POSITION + " ASC, " + CATEGORY_ID + " DESC";

        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(selectAllProducts, null);

        if (cursor.moveToFirst()) {
            do {
                CategoryBean category = new CategoryBean();
                category.setCategoryId(Integer.parseInt(cursor.getString(0)));
                category.setCategoryName(cursor.getString(1));
                category.setCategoryParentId(Integer.parseInt(cursor.getString(2)));
                category.setCategoryDesc(cursor.getString(3));
                category.setCategoryImage(cursor.getString(4));
                category.setImageId(Integer.parseInt(cursor.getString(5)));
                category.setImageServerPath(cursor.getString(6));
                category.setImageLocalPath(cursor.getString(7));
                category.setCategoryPosition(Integer.parseInt(cursor.getString(8)));
                // Adding product to list
                categoryList.add(category);
            } while (cursor.moveToNext());
        }

        if (cursor != null)
            cursor.close();
        if (categoryList.size() > 0)
            return categoryList;
        else
            return null;
    }

    public boolean checkProductIsExist(int productId) {
        String queryCheck = "SELECT * FROM " + PRODUCTS_TABLE + " WHERE " + PRODUCT_ID + " = " + productId;
        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(queryCheck, null);
        return cursor.getCount() == 1;
    }

    public List<ProductBean> getNewProducts() {
        List<ProductBean> productsList = new ArrayList<ProductBean>();
//        String selectProductsByCategory = "SELECT * FROM " + PRODUCTS_TABLE + " WHERE " + IS_NEW_PRODUCT + " =1 ORDER BY " + PRODUCT_ID + " DESC";
        String selectProductsByCategory = "SELECT * FROM " + PRODUCTS_TABLE + " WHERE " + IS_NEW_PRODUCT + " =1 ORDER BY " + LAUNCH_DATE + " DESC";
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(selectProductsByCategory, null);

        if (cursor.moveToFirst()) {
            do {
                ProductBean product = new ProductBean();
                product.setId(Integer.parseInt(cursor.getString(0)));
                product.setProductName(cursor.getString(1));
                product.setVaultDocsJson(cursor.getString(2));
                product.setProductDesc(cursor.getString(3));
                product.setProductDesc1(cursor.getString(4));
                product.setProductDesc2(cursor.getString(6));
                product.setCategoryId(Integer.parseInt(cursor.getString(7)));
                product.setProductImage(cursor.getString(8));
                product.setProductImageServerPath(cursor.getString(9));
                product.setProductImageLocalPath(cursor.getString(10));
                product.setProductImagesJson(cursor.getString(11));
                product.setImageId(Integer.parseInt(cursor.getString(12)));
                product.setIsNewProduct(cursor.getString(5));
                product.setLaunch_date(cursor.getString(13));

                // Adding product to list
                productsList.add(product);
            } while (cursor.moveToNext());
        }
        if (cursor != null)
            cursor.close();

        if (productsList.size() > 0)
            return productsList;
        else
            return null;
    }



    public List<ProductBean> getProductsByCategory(int catId) {
        List<ProductBean> productsList = new ArrayList<ProductBean>();
        String selectProductsByCategory = "SELECT * FROM " + PRODUCTS_TABLE + " where " + PRODUCT_CAT_ID + "=" + catId + " ORDER BY " + LAUNCH_DATE + " DESC , " + IS_NEW_PRODUCT + " DESC ";
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(selectProductsByCategory, null);
        if (cursor.moveToFirst()) {
            do {
                ProductBean product = new ProductBean();
                product.setId(Integer.parseInt(cursor.getString(0)));
                product.setProductName(cursor.getString(1));
                // product.setProductName(cursor.getString(2));
                product.setVaultDocsJson(cursor.getString(2));
                product.setProductDesc(cursor.getString(3));
                product.setProductDesc1(cursor.getString(4));
                product.setProductDesc2(cursor.getString(5));
                product.setCategoryId(Integer.parseInt(cursor.getString(7)));
                product.setProductImage(cursor.getString(8));
                product.setProductImageServerPath(cursor.getString(9));
                product.setProductImageLocalPath(cursor.getString(10));
                product.setProductImagesJson(cursor.getString(11));
                product.setImageId(Integer.parseInt(cursor.getString(12)));
                product.setIsNewProduct(cursor.getString(5));

                // Adding product to list
                productsList.add(product);
            } while (cursor.moveToNext());
        }
        if (cursor != null)
            cursor.close();

        if (productsList.size() > 0)
            return productsList;
        else
            return null;
    }

    public ProductBean getProductById(int productId) {
        ProductBean product = null;
        try {
            String selectAllProducts = "SELECT * FROM " + PRODUCTS_TABLE + " WHERE " + PRODUCT_ID + " = " + productId;

            SQLiteDatabase db = this.getReadableDatabase();
            Cursor cursor = db.rawQuery(selectAllProducts, null);

            if (cursor.moveToFirst()) {
                product = new ProductBean();
                product.setId(Integer.parseInt(cursor.getString(0)));
                product.setProductName(cursor.getString(1));
                product.setVaultDocsJson(cursor.getString(2));
                product.setProductDesc(cursor.getString(3));
                product.setProductDesc1(cursor.getString(4));
                product.setProductDesc2(cursor.getString(6));
                product.setCategoryId(Integer.parseInt(cursor.getString(7)));
                product.setProductImage(cursor.getString(8));
                product.setProductImageServerPath(cursor.getString(9));
                product.setProductImageLocalPath(cursor.getString(10));
                product.setProductImagesJson(cursor.getString(11));
                product.setImageId(Integer.parseInt(cursor.getString(12)));
                product.setIsNewProduct(cursor.getString(5));
            }
            if (cursor != null)
                cursor.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return product;
    }

    public ArrayList<ProductBean> getAllProducts() {
        ArrayList<ProductBean> productsList = new ArrayList<ProductBean>();
        try {
            String selectAllProducts = "SELECT * FROM " + PRODUCTS_TABLE;

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectAllProducts, null);

            if (cursor.moveToFirst()) {
                do {
                    ProductBean product = new ProductBean();
                    product.setId(Integer.parseInt(cursor.getString(0)));
                    product.setProductName(cursor.getString(1));
                    product.setVaultDocsJson(cursor.getString(2));
                    product.setProductDesc(cursor.getString(3));
                    product.setProductDesc1(cursor.getString(4));
                    product.setProductDesc2(cursor.getString(6));
                    product.setCategoryId(Integer.parseInt(cursor.getString(7)));
                    product.setProductImage(cursor.getString(8));
                    product.setProductImageServerPath(cursor.getString(9));
                    product.setProductImageLocalPath(cursor.getString(10));
                    product.setProductImagesJson(cursor.getString(11));
                    product.setImageId(Integer.parseInt(cursor.getString(12)));
                    product.setIsNewProduct(cursor.getString(5));

                    // Adding product to list
                    productsList.add(product);
                } while (cursor.moveToNext());
            }
            if (cursor != null)
                cursor.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return productsList;
    }


    public List<DocumentBean> getDocumentsByProductId(int id) {
        List<DocumentBean> documentList = new ArrayList<DocumentBean>();
        String selectAllDocuments = "SELECT * FROM " + DOC_TABLE + " where " + DOC_PRODUCT_ID + "=" + id;

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectAllDocuments, null);

        if (cursor.moveToFirst()) {
            do {
                DocumentBean document = new DocumentBean();
                document.setDocId(Integer.parseInt(cursor.getString(0)));
                document.setDocType(cursor.getString(1));
                document.setDocName(cursor.getString(2));
                document.setDocUrl(cursor.getString(3));
                document.setDocLocalPath(cursor.getString(4));
                document.setProductId(Integer.parseInt(cursor.getString(5)));

                // Adding product to list
                documentList.add(document);
            } while (cursor.moveToNext());
        }
        if (cursor != null)
            cursor.close();
        if (documentList.size() > 0)
            return documentList;
        else
            return null;
    }

    /*
    * DELETING USER INTO DATABASE
    * */
    public synchronized void deleteUser() {
        SQLiteDatabase db = this.getWritableDatabase();
        long i = db.delete(USER_TABLE, null, null);

        if (i > -1)
            System.out.print("Successfully deleted user");
        else
            System.out.print("Fail to delete user");
        db.close();
    }


    public synchronized void deleteCategoryById(int categoryId) {
        SQLiteDatabase db = this.getWritableDatabase();
        String whereArgs[] = {Integer.toString(categoryId)};

        long i = db.delete(CATEGORY_TABLE, CATEGORY_ID + "= ?", whereArgs);
        if (i > -1)
            System.out.print("Successfully deleted category");
        else
            System.out.print("Fail to delete category");

        db.close(); // Closing database connection
    }

    public void deleteAllTutorialCategory() {
        SQLiteDatabase db = getWritableDatabase();
        db.execSQL("DELETE FROM " + TABLE_GET_DATA + " WHERE " + COL_DATA_TYPE + " = '" + TYPE_TUTORIAL_CATEGORY + "'");
        db.close();
    }

    public void deleteAllAuditTrailData(int userid) {
        SQLiteDatabase db = getWritableDatabase();
        db.execSQL("DELETE FROM " + TABLE_MODULE_AUDIT_TRAIL + " WHERE " + COL_USER_ID + " = '" + userid + "'");
        db.close();
    }

    public void deleteAllREeadData(int userid) {
        SQLiteDatabase db = getWritableDatabase();
        db.execSQL("DELETE FROM " + TABLE_TOPIC_READ + " WHERE " + COL_USER_ID + " = '" + userid + "'");
        db.close();
    }

    public void deleteAllUpdateCategory() {
        SQLiteDatabase db = getWritableDatabase();
        db.execSQL("DELETE FROM " + TABLE_GET_DATA + " WHERE " + COL_DATA_TYPE + " = '" + TYPE_UPDATE_CATEGORY + "'");
        db.close();
    }

    public void deleteAllRecommModelData() {
        SQLiteDatabase db = getWritableDatabase();
        db.execSQL("DELETE FROM " + TABLE_GET_DATA + " WHERE " + COL_DATA_TYPE + " = '" + TYPE_RECOMM_MODEL + "'");
        db.close();
    }

    public void deleteAllRecommAttribData() {
        SQLiteDatabase db = getWritableDatabase();
        db.execSQL("DELETE FROM " + TABLE_GET_DATA + " WHERE " + COL_DATA_TYPE + " = '" + TYPE_RECOMM_ATTRIB + "'");
        db.close();
    }

    public void deleteSubmitLogout() {
        SQLiteDatabase db = getWritableDatabase();
        db.execSQL("DELETE FROM " + TABLE_SUBMIT_DATA + " WHERE " + COL_DATA_TYPE + " = '" + TYPE_LOGOUT + "'");
        db.close();
    }

    public void deleteProductData() {
        SQLiteDatabase db = this.getWritableDatabase();
        db.execSQL("delete from " + PRODUCTS_TABLE);
        db.close();
    }

    public void deleteCategoryData() {
        SQLiteDatabase db = this.getWritableDatabase();
        db.execSQL("delete from " + CATEGORY_TABLE);
        db.close();
    }

    public void deleteNewProductData() {
        SQLiteDatabase db = this.getWritableDatabase();
        db.execSQL("delete from " + NEW_PRODUCTS_TABLE);
        db.close();
    }

    public synchronized void deleteProductById(int productId) {
        SQLiteDatabase db = this.getWritableDatabase();
        String whereArgs[] = {Integer.toString(productId)};

        long i = db.delete(PRODUCTS_TABLE, PRODUCT_ID + "= ?", whereArgs);
        if (i > -1)
            System.out.print("Successfully deleted category");
        else
            System.out.print("Fail to delete category");
        db.close(); // Closing database connection
    }

    public synchronized void deleteAllTable() {
        SQLiteDatabase db = this.getWritableDatabase();
        db.delete(USER_TABLE, null, null);
        db.delete(CATEGORY_TABLE, null, null);
        db.delete(PRODUCTS_TABLE, null, null);
        db.delete(DOC_TABLE, null, null);
        db.delete(IMAGE_TABLE, null, null);
        db.delete(APP_DATA_TABLE, null, null);
        db.delete(NEW_PRODUCTS_TABLE, null, null);
        db.delete(TABLE_GET_DATA, null, null);
        db.close();
    }
}
