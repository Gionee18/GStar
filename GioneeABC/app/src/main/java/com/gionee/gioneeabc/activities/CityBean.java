package com.gionee.gioneeabc.activities;

import java.util.ArrayList;

/**
 * Created by root on 24/10/16.
 */
public class CityBean {
    private int count;
    private String status;
    public ArrayList<DataCity> data;

    public int getCount() {
        return count;
    }

    public void setCount(int count) {
        this.count = count;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public ArrayList<DataCity> getData() {
        return data;
    }

    public void setData(ArrayList<DataCity> data) {
        this.data = data;
    }

    public class DataCity {
        private int city_id;
        private String city_name;

        public int getState_id() {
            return city_id;
        }

        public void setState_id(int city_id) {
            this.city_id = city_id;
        }

        public String getState_name() {
            return city_name;
        }

        public void setState_name(String city_name) {
            this.city_name = city_name;
        }
    }
}
