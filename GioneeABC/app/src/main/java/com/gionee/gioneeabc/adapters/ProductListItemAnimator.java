package com.gionee.gioneeabc.adapters;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.support.v7.widget.DefaultItemAnimator;
import android.support.v7.widget.RecyclerView;
import android.view.animation.DecelerateInterpolator;

import com.gionee.gioneeabc.helpers.Util;

/**
 * Created by Linchpin25 on 2/18/2016.
 */
public class ProductListItemAnimator extends DefaultItemAnimator {

    private int lastAddAnimatedItem = -2;

    @Override
    public boolean canReuseUpdatedViewHolder(RecyclerView.ViewHolder viewHolder) {
        return true;
    }

    @Override
    public boolean animateAdd(RecyclerView.ViewHolder holder) {

        if (holder.getLayoutPosition() > lastAddAnimatedItem) {
            lastAddAnimatedItem++;
            runEnterAnimation((ProductListAdapter.CustomViewHolder) holder);
            return false;

        }
        return false;
    }

    private void runEnterAnimation(final ProductListAdapter.CustomViewHolder holder)
    {
        final int screenHeight= Util.getScreenHeight(holder.itemView.getContext());
        holder.itemView.setTranslationY(screenHeight);
        holder.itemView.animate()
                .translationY(0)
                .setInterpolator(new DecelerateInterpolator(3.f))
                .setDuration(700)
                .setListener(new AnimatorListenerAdapter() {
                    @Override
                    public void onAnimationEnd(Animator animation) {
                        dispatchAddFinished(holder);
                    }
                })
                .start();
    }


}
