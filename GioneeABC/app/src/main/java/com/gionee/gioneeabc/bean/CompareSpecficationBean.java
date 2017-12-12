package com.gionee.gioneeabc.bean;

import java.util.ArrayList;

/**
 * Created by admin on 23-12-2016.
 */
public class CompareSpecficationBean {
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

    private ArrayList<CompareData> data;

    public ArrayList<CompareData> getData() {
        return this.data;
    }

    public void setData(ArrayList<CompareData> data) {
        this.data = data;
    }

    public String selectedGioneeModel;
    public String selectedNonGioneeModel;

    public String getSelectedGioneeModel() {
        return selectedGioneeModel;
    }

    public void setSelectedGioneeModel(String selectedGioneeModel) {
        this.selectedGioneeModel = selectedGioneeModel;
    }

    public String getSelectedNonGioneeModel() {
        return selectedNonGioneeModel;
    }

    public void setSelectedNonGioneeModel(String selectedNonGioneeModel) {
        this.selectedNonGioneeModel = selectedNonGioneeModel;
    }

    public int selectedGioneeModelId;
    public int selectedNonGioneeModelId;

    public int getSelectedGioneeModelId() {
        return selectedGioneeModelId;
    }

    public void setSelectedGioneeModelId(int selectedGioneeModelId) {
        this.selectedGioneeModelId = selectedGioneeModelId;
    }

    public int getSelectedNonGioneeModelId() {
        return selectedNonGioneeModelId;
    }

    public void setSelectedNonGioneeModelId(int selectedNonGioneeModelId) {
        this.selectedNonGioneeModelId = selectedNonGioneeModelId;
    }

    public class CompareData {
        private int id;

        public int getId() {
            return this.id;
        }

        public void setId(int id) {
            this.id = id;
        }

        private String cat_name;

        public String getCatName() {
            return this.cat_name;
        }

        public void setCatName(String cat_name) {
            this.cat_name = cat_name;
        }

        private ArrayList<CompareSubcategory> subcategory;

        public ArrayList<CompareSubcategory> getSubcategory() {
            return this.subcategory;
        }

        public void setSubcategory(ArrayList<CompareSubcategory> subcategory) {
            this.subcategory = subcategory;
        }
    }

    public class CompareSubcategory {
        private String cat_name;

        public String getCatName() {
            return this.cat_name;
        }

        public void setCatName(String cat_name) {
            this.cat_name = cat_name;
        }

        private int subcatid;

        public int getSubcatid() {
            return this.subcatid;
        }

        public void setSubcatid(int subcatid) {
            this.subcatid = subcatid;
        }

        private String subcat_name;

        public String getSubcatName() {
            return this.subcat_name;
        }

        public void setSubcatName(String subcat_name) {
            this.subcat_name = subcat_name;
        }

        private String gionee;

        public String getGionee() {
            return this.gionee;
        }

        public void setGionee(String gionee) {
            this.gionee = gionee;
        }

        private String other;

        public String getOther() {
            return this.other;
        }

        public void setOther(String other) {
            this.other = other;
        }
    }

}
