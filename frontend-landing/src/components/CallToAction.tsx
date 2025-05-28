// src/components/CallToAction.tsx
import React from 'react';
import styles from './CallToAction.module.scss';

const CallToAction: React.FC = () => {
  return (
    <section className={styles.callToAction}>
      <div className="container">
        <h2>Take the Next Step: Join Our Exclusive Conference</h2>
        <p>Join us at our upcoming conference to learn more and get started with CrediCarbSf. Network with industry leaders, discover new opportunities, and take the next step in your carbon credit journey.</p>
        <p className={styles.incentiveText}>Exclusive conference access to premium trading tools!</p>
        <a href="#conference-signup" className={styles.ctaButton}>Get Started During Your Conference</a>
      </div>
    </section>
  );
};
export default CallToAction;
