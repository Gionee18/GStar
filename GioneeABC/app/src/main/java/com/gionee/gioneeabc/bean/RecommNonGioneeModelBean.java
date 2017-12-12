package com.gionee.gioneeabc.bean;

import java.io.Serializable;
import java.util.ArrayList;

/**
 * Created by admin on 15-12-2016.
 */
public class RecommNonGioneeModelBean implements Serializable{
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

    private ArrayList<RecommNonGioneeModeData> data;

    public ArrayList<RecommNonGioneeModeData> getData() {
        return this.data;
    }

    public void setData(ArrayList<RecommNonGioneeModeData> data) {
        this.data = data;
    }

    private String error;

    public String getError() {
        return error;
    }

    public void setError(String error) {
        this.error = error;
    }

    public class RecommNonGioneeModeData implements Serializable{
        private int id;

        public int getId() {
            return this.id;
        }

        public void setId(int id) {
            this.id = id;
        }

        private String name;

        public String getName() {
            return this.name;
        }

        public void setName(String name) {
            this.name = name;
        }

        private ArrayList<AssetImage> asset_image;

        public ArrayList<AssetImage> getAssetImage() {
            return this.asset_image;
        }

        public void setAssetImage(ArrayList<AssetImage> asset_image) {
            this.asset_image = asset_image;
        }
        private ArrayList<Model> model;

        public ArrayList<Model> getModel() { return this.model; }

        public void setModel(ArrayList<Model> model) { this.model = model; }

        private boolean isSelected;

        public boolean isSelected() {
            return isSelected;
        }

        public void setIsSelected(boolean isSelected) {
            this.isSelected = isSelected;
        }
    }

    public class AssetImage implements Serializable {
        private int image_id;

        public int getImageId() {
            return this.image_id;
        }

        public void setImageId(int image_id) {
            this.image_id = image_id;
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

    public class Model implements Serializable {
        private int id;

        public int getId() {
            return this.id;
        }

        public void setId(int id) {
            this.id = id;
        }

        private String model_name;

        public String getModelName() {
            return this.model_name;
        }

        public void setModelName(String model_name) {
            this.model_name = model_name;
        }

        private ArrayList<AssetImage> asset_image;

        public ArrayList<AssetImage> getAssetImage() {
            return this.asset_image;
        }

        public void setAssetImage(ArrayList<AssetImage> asset_image) {
            this.asset_image = asset_image;
        }

        private boolean isSelected;

        public boolean isSelected() {
            return isSelected;
        }

        public void setIsSelected(boolean isSelected) {
            this.isSelected = isSelected;
        }
    }
}