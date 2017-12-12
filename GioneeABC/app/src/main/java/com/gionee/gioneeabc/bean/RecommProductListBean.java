package com.gionee.gioneeabc.bean;

import java.util.ArrayList;

/**
 * Created by Linchpin
 */
public class RecommProductListBean {
    private int count;

    public int getCount() {
        return this.count;
    }

    public void setCount(int count) {
        this.count = count;
    }

    private String status;

    public String getStatus() {
        return this.status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    private ArrayList<Datum> data;

    public ArrayList<Datum> getData() {
        return this.data;
    }

    public void setData(ArrayList<Datum> data) {
        this.data = data;
    }

    public class ProAsset {
        private int asset_id;

        public int getAssetId() {
            return this.asset_id;
        }

        public void setAssetId(int asset_id) {
            this.asset_id = asset_id;
        }

        private String name;

        public String getName() {
            return this.name;
        }

        public void setName(String name) {
            this.name = name;
        }

        private String path;

        public String getPath() {
            return this.path;
        }

        public void setPath(String path) {
            this.path = path;
        }
    }

    public class Datum {
        private int id;

        public int getId() {
            return this.id;
        }

        public void setId(int id) {
            this.id = id;
        }

        private String product_name;

        public String getProductName() {
            return this.product_name;
        }

        public void setProductName(String product_name) {
            this.product_name = product_name;
        }

        private String new_product_flag;

        public String getNewProductFlag() {
            return this.new_product_flag;
        }

        public void setNewProductFlag(String new_product_flag) {
            this.new_product_flag = new_product_flag;
        }

        private int category_id;

        public int getCategoryId() {
            return this.category_id;
        }

        public void setCategoryId(int category_id) {
            this.category_id = category_id;
        }

        private String category_name;

        public String getCategoryName() {
            return this.category_name;
        }

        public void setCategoryName(String category_name) {
            this.category_name = category_name;
        }

        private String price;

        public String getPrice() {
            return this.price;
        }

        public void setPrice(String price) {
            this.price = price;
        }

        private ArrayList<ProAsset> pro_asset;

        public ArrayList<ProAsset> getProAsset() {
            return this.pro_asset;
        }

        public void setProAsset(ArrayList<ProAsset> pro_asset) {
            this.pro_asset = pro_asset;
        }

        private String launch_date;

        public String getLaunch_date() {
            return launch_date;
        }

        public void setLaunch_date(String launch_date) {
            this.launch_date = launch_date;
        }


//        @Override
//        public int compareTo(Datum datum) {
//            if (getLaunch_date() == null || datum.getLaunch_date() == null)
//                return 0;
//            SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
//            Date date1 = null;
//            Date date2 = null;
//            try {
//                date1 = dateFormat.parse(getLaunch_date());
//                date2 = dateFormat.parse(datum.getLaunch_date());
//
//                return date1.compareTo(date2);
//
//            } catch (ParseException e) {
//                e.printStackTrace();
//            }
//            return 0;
//        }

    }
}
